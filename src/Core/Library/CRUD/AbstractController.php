<?php

namespace WS\Core\Library\CRUD;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Library\DataExport\DataExportInterface;
use WS\Core\Library\DataExport\Provider\CsvExportProvider;
use WS\Core\Library\Traits\CRUD\AssetTrait;
use WS\Core\Library\Traits\CRUD\CrudTrait;
use WS\Core\Library\Traits\CRUD\RoleCalculatorTrait;
use WS\Core\Library\Traits\CRUD\RouteNamePrefixTrait;
use WS\Core\Library\Traits\CRUD\TemplateTrait;
use WS\Core\Library\Traits\CRUD\TranslatorTrait;
use WS\Core\Service\DataExportService;
use WS\Core\Service\FileService;
use WS\Core\Service\ImageService;

abstract class AbstractController extends BaseController
{
    use RoleCalculatorTrait;
    use TranslatorTrait;
    use RouteNamePrefixTrait;
    use TemplateTrait;
    use CrudTrait;
    use AssetTrait;

    public const DELETE_BATCH_ACTION = 'delete.batch_action';

    protected TranslatorInterface $translator;
    protected ImageService $imageService;
    protected FileService $fileService;
    protected DataExportService $dataExportService;
    protected AbstractService $service;
    protected EntityManagerInterface $doctrine;

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function setImageService(ImageService $imageService): void
    {
        $this->imageService = $imageService;
    }

    public function setFileService(FileService $fileService): void
    {
        $this->fileService = $fileService;
    }

    public function setDataExportService(DataExportService $dataExportService): void
    {
        $this->dataExportService = $dataExportService;
    }

    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }

    protected function getService(): AbstractService
    {
        return $this->service;
    }

    protected function denyAccessUnlessAllowed(string $action): void
    {
        if (!$this->isGranted($this->calculateRole($this->getService()->getEntityClass(), $action))) {
            $exception = $this->createAccessDeniedException($this->trans('not_allowed', [], 'ws_cms'));
            throw $exception;
        }
    }

    protected function getFormErrorMessagesList(FormInterface $form, int $output = 0): mixed
    {
        $errors = [];

        /** @var FormError $error */
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        if ($output == 0) {
            return implode(PHP_EOL, $errors);
        }

        return $errors;
    }

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessAllowed('view');

        $page = intval($request->get('page', 1));
        if ($page < 1) {
            $page = 1;
        }

        $limit = intval($request->get('limit', $this->getLimit()));
        if (!$limit) {
            $limit = $this->getLimit();
        }

        // Search simple
        $search = strval($request->get('f'));

        // Filter extended
        $filterExtended = $this->getFilterExtendedForm();
        $filterExtendedView = null;
        $filterExtendedData = null;

        if ($filterExtended instanceof Form) {
            $filterExtended->handleRequest($request);
            if ($filterExtended->isSubmitted() && $filterExtended->isValid()) {
                /** @var ?array */
                $filterExtendedData = $filterExtended->getData();
            }
            $filterExtendedView = $filterExtended->createView();
        }

        // Retrieve data
        $data = $this->indexFetchData(
            $search,
            $filterExtendedData,
            $page,
            $limit,
            strval($request->get('sort')),
            strval($request->get('dir'))
        );

        // Calculate pagination
        $paginationData = [
            'currentPage' => $page,
            'url' => $request->get('_route'),
            'nbPages' => ceil($data['total'] / $limit),
            'currentCount' => count($data['data']),
            'totalCount' => $data['total'],
            'limit' => $limit
        ];

        // Mark sortable fields
        $listFields = $this->getService()->getListFields();
        $sortFields = $this->getService()->getSortFields();
        $sortFields = array_merge(array_keys($sortFields), array_values($sortFields));
        $sortFields = array_filter($sortFields, function ($sortField) {
            return !is_numeric($sortField);
        });

        foreach ($listFields as &$field) {
            $field['sortable'] = false;
            if (in_array($field['name'], $sortFields)) {
                $field['sortable'] = true;
            }
        }

        // Add data to the view
        $extraData = $this->indexExtraData();

        // Define CRUD roles
        $viewRoles = [
            'create' => $this->calculateRole($this->getService()->getEntityClass(), 'create'),
            'edit' => $this->calculateRole($this->getService()->getEntityClass(), 'edit'),
            'delete' => $this->calculateRole($this->getService()->getEntityClass(), 'delete')
        ];

        return $this->render(
            $this->getTemplate('index.html.twig'),
            array_merge(
                $data,
                [
                    'sort' => $request->query->get('sort'),
                    'dir' => $request->query->get('dir'),
                    'paginationData' => $paginationData,
                    'params' => $request->query->all(),
                    'trans_prefix' => $this->getTranslatorPrefix(),
                    'route_prefix' => $this->getRouteNamePrefix(),
                    'list_fields' => $listFields,
                    'batch_actions' => $this->getBatchActions(),
                    'filter_extended_form' => $filterExtendedView,
                    'view_roles' => $viewRoles,
                    'view_export' => ($this->getService() instanceof DataExportInterface),
                ],
                $extraData
            )
        );
    }

    #[Route(path: '/create', name: 'create')]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessAllowed('create');

        // Create new Entity
        $entity = $this->createEntity($request);
        if ($entity === null) {
            throw new BadRequestHttpException($this->trans('bad_request', [], 'ws_cms'));
        }

        // Create entity Form
        $form = $this->createEntityForm($entity);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->getService()->create($entity);

                    $this->handleImages($form, $entity);

                    $this->handleFiles($form, $entity);

                    $this->addFlash('cms_success', $this->trans('create_success', [], $this->getTranslatorPrefix()));

                    if ($form->has('saveAndBack')) {
                        $submitButton = $form->get('saveAndBack');
                        if ($submitButton instanceof SubmitButton && $submitButton->isClicked()) {
                            if ($form->has('referer')) {
                                return $this->redirect(\strval($form->get('referer')->getData()));
                            }

                            return $this->redirect($this->wsGenerateUrl($this->getRouteNamePrefix() . '_index'));
                        }
                    }

                    if ($this->isGranted($this->calculateRole($this->getService()->getEntityClass(), 'edit'))) {
                        return $this->redirect(
                            $this->wsGenerateUrl($this->getRouteNamePrefix() . '_edit', [
                                'id' => (method_exists($entity, 'getId')) ? $entity->getId() : null
                            ])
                        );
                    } else {
                        return $this->redirect($this->wsGenerateUrl($this->getRouteNamePrefix() . '_index'));
                    }
                } catch (\Exception) {
                    $this->addFlash('cms_error', $this->trans('create_error', [], $this->getTranslatorPrefix()));
                }
            } else {
                $this->addFlash('cms_error', $this->getFormErrorMessagesList($form));
            }
        }

        // Add data to the view
        $extraData = $this->createExtraData();

        return $this->render(
            $this->getTemplate('show.html.twig'),
            array_merge(
                [
                    'form' => $form->createView(),
                    'isCreate' => true,
                    'trans_prefix' => $this->getTranslatorPrefix(),
                    'route_prefix' => $this->getRouteNamePrefix(),
                ],
                $extraData
            )
        );
    }

    #[Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, int $id): Response
    {
        $this->denyAccessUnlessAllowed('edit');

        // Get entity
        $entity = $this->editEntity($id);
        if ($entity === null || get_class($entity) !== $this->getService()->getEntityClass()) {
            throw new NotFoundHttpException(sprintf($this->trans('not_found', [], $this->getTranslatorPrefix()), $id));
        }

        // Create entity Form
        $form = $this->editEntityForm($entity);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->getService()->edit($entity);

                    $this->handleImages($form, $entity);

                    $this->handleFiles($form, $entity);

                    $this->addFlash('cms_success', $this->trans('edit_success', [], $this->getTranslatorPrefix()));

                    if ($form->has('saveAndBack')) {
                        $submitButton = $form->get('saveAndBack');
                        if ($submitButton instanceof SubmitButton && $submitButton->isClicked()) {
                            if ($form->has('referer')) {
                                return $this->redirect(\strval($form->get('referer')->getData()));
                            }

                            return $this->redirect($this->wsGenerateUrl($this->getRouteNamePrefix() . '_index'));
                        }
                    }

                    return $this->redirect(
                        $this->wsGenerateUrl($this->getRouteNamePrefix() . '_edit', [
                            'id' => (method_exists($entity, 'getId')) ? $entity->getId() : null
                        ])
                    );
                } catch (\Exception) {
                    $this->addFlash('cms_error', $this->trans('edit_error', [], $this->getTranslatorPrefix()));
                }
            } else {
                $this->addFlash('cms_error', $this->getFormErrorMessagesList($form));
            }
        }

        // Add data to the view
        $extraData = $this->editExtraData();

        return $this->render(
            $this->getTemplate('show.html.twig'),
            array_merge(
                [
                    'form' => $form->createView(),
                    'isCreate' => false,
                    'trans_prefix' => $this->getTranslatorPrefix(),
                    'route_prefix' => $this->getRouteNamePrefix(),
                ],
                $extraData
            )
        );
    }

    #[Route(path: '/delete/{id}', name: 'delete', methods: 'POST')]
    public function delete(Request $request, int $id): Response
    {
        try {
            $this->denyAccessUnlessAllowed('delete');
        } catch (AccessDeniedException $exception) {
            return $this->json(['msg' => $this->trans('not_allowed', [], 'ws_cms')], Response::HTTP_FORBIDDEN);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->json(
                ['msg' => $this->trans('bad_request', [], 'ws_cms')],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $entity = $this->getService()->get($id);
            if ($entity === null || get_class($entity) !== $this->getService()->getEntityClass()) {
                return $this->json([
                    'msg' => sprintf($this->trans('not_found', [], $this->getTranslatorPrefix()), $id)
                ], Response::HTTP_NOT_FOUND);
            }

            $this->getService()->delete($entity);

            return $this->json([
                'id' => $id,
                'title' => $this->trans('delete_title_success', [], 'ws_cms'),
                'msg' => $this->trans('delete_success', [], $this->getTranslatorPrefix()),
            ], Response::HTTP_OK);
        } catch (\Exception) {
            return $this->json([
                'msg' => $this->trans('delete_failed', [], 'ws_cms')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/batch/delete', name: 'batch_delete', methods: 'POST')]
    public function batchDelete(Request $request): Response
    {
        try {
            $this->denyAccessUnlessAllowed('delete');
        } catch (AccessDeniedException) {
            return $this->json(['msg' => $this->trans('not_allowed', [], 'ws_cms')], Response::HTTP_FORBIDDEN);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->json(
                ['msg' => $this->trans('bad_request', [], 'ws_cms')],
                Response::HTTP_BAD_REQUEST
            );
        }
        /** @var array */
        $params = json_decode((string) $request->getContent(), true);
        if (!isset($params['ids']) || empty($params['ids'])) {
            return $this->json(
                ['msg' => $this->trans('bad_request', [], 'ws_cms')],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->getService()->batchDelete($params['ids']);

            return $this->json([
                'msg' => $this->trans('batch_action.success_message', [], 'ws_cms'),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'msg' => $this->trans('batch_action.fail_message', [], 'ws_cms')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/export', name: 'export', methods: 'GET')]
    public function export(Request $request): Response
    {
        $this->denyAccessUnlessAllowed('view');

        $service = $this->getService();
        if (!$service instanceof DataExportInterface) {
            throw new NotFoundHttpException();
        }

        // Search simple
        $search = strval($request->get('f'));

        // Filter extended
        $filterExtended = $this->getFilterExtendedForm();
        $filterExtendedData = null;

        if ($filterExtended instanceof Form) {
            $filterExtended->handleRequest($request);
            if ($filterExtended->isSubmitted() && $filterExtended->isValid()) {
                /** @var ?array */
                $filterExtendedData = $filterExtended->getData();
            }
        }

        // Retrieve data
        $data = $service->getDataExport(
            $search,
            $filterExtendedData,
            strval($request->get('sort')),
            strval($request->get('dir'))
        );

        // Set format
        $format = strtolower(strval($request->get('format', CsvExportProvider::EXPORT_FORMAT)));

        $content = $this->dataExportService->export($data, $format);
        $headers = $this->dataExportService->headers($format);

        $response = new Response($content);
        foreach ($headers as $header) {
            $response->headers->set($header['name'], $header['value']);
        }

        if (!$response->headers->has('Content-Disposition')) {
            $currentDatetime = new \DateTimeImmutable();
            $filename = sprintf(
                '%s-export-%s.%s',
                str_replace(['ws_cms_', 'cms_'], [''], $this->getRouteNamePrefix()),
                $currentDatetime->format('YmdHis'),
                $format
            );

            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
        }

        return $response;
    }
}
