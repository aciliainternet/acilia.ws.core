<?php

namespace WS\Core\Library\CRUD;

use Symfony\Component\Form\Form;
use WS\Core\Service\FileService;
use WS\Core\Service\ImageService;
use Symfony\Component\Form\FormError;
use WS\Core\Service\DataExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WS\Core\Library\DataExport\DataExportInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Library\DataExport\Provider\CsvExportProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;

abstract class AbstractController extends BaseController
{
    const EVENT_INDEX_FETCH_DATA = 'index.fetch_data';
    const EVENT_INDEX_EXTRA_DATA = 'index.extra_data';
    const EVENT_CREATE_NEW_ENTITY = 'create.new_entity';
    const EVENT_CREATE_CREATE_FORM = 'create.create_form';
    const EVENT_CREATE_EXTRA_DATA = 'create.extra_data';
    const EVENT_EDIT_CREATE_FORM = 'edit.create_form';
    const EVENT_EDIT_EXTRA_DATA = 'edit.extra_data';
    const EVENT_EXPORT_FETCH_DATA = 'export.fetch.data';
    const EVENT_IMAGE_HANDLE = 'image_handle';
    const EVENT_FILE_HANDLE = 'file_handle';

    const DELETE_BATCH_ACTION = 'delete.batch_action';

    use RoleCalculatorTrait;
    use TranslatorTrait;
    use RouteTrait;

    protected TranslatorInterface $translator;
    protected ImageService $imageService;
    protected FileService $fileService;
    protected DataExportService $dataExportService;
    protected array $events = [];
    protected AbstractService $service;
    protected EntityManagerInterface $doctrine;

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
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

    protected function getService(): AbstractService
    {
        return $this->service;
    }

    protected function getBatchActions(): array
    {
        return [];
    }

    public function trans(string $id, array $parameters = array(), ?string $domain = null, ?string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    protected function addEvent(string $event, \Closure $callback): void
    {
        $this->events[$event] = $callback;
    }

    protected function getLimit(): int
    {
        return 20;
    }

    protected function useCRUDTemplate(string $template): bool
    {
        if ($template == 'index.html.twig') {
            return true;
        }

        if ($template == 'show.html.twig') {
            return true;
        }

        return false;
    }

    protected function getTemplate(string $template, ?object $entity = null): string
    {
        $routePrefix = '';
        $controllerClass = get_class($this);
        $classPath = explode('\\', $controllerClass);

        if ($classPath[0] === 'WS') {
            $controllerName = strtolower(str_replace('Controller', '', $classPath[4]));
            $routePrefix = sprintf('@%s%s/%s/%s', $classPath[0], $classPath[1], strtolower($classPath[3]), $controllerName);
        }

        $templateFile = sprintf('%s/%s', $routePrefix, $template);
        if ($this->useCRUDTemplate($template)) {
            if (!$this->container->get('twig')->getLoader()->exists($templateFile)) {
                $templateFile = sprintf('@WSCore/cms/crud/%s', $template);
            }
        }

        return $templateFile;
    }

    protected function denyAccessUnlessAllowed(string $action): void
    {
        if (!$this->isGranted($this->calculateRole($this->getService()->getEntityClass(), $action))) {
            $exception = $this->createAccessDeniedException($this->trans('not_allowed', [], 'ws_cms'));
            throw $exception;
        }
    }

    protected function handleImages(FormInterface $form, object $entity): void
    {
        if ($this->getService()->getImageFields($entity)) {
            foreach ($this->getService()->getImageFields($entity) as $imageField) {
                if (!empty($form->get($imageField)->get('asset')->getData())) {
                    $imageFile = $form->get($imageField)->get('asset')->getData();
                    $options = [
                        'cropper' => $form->get($imageField)->get('cropper')->getData()
                    ];

                    $assetImage = $this->imageService->handle($entity, $imageField, $imageFile, $options, $this->getService()->getImageEntityClass($entity));
                    if (isset($this->events[self::EVENT_IMAGE_HANDLE])) {
                        $this->events[self::EVENT_IMAGE_HANDLE]($entity, $imageField, $assetImage);
                    }
                } elseif ($form->get($imageField)->has('asset_data') && is_numeric($form->get($imageField)->get('asset_data')->getData())) {
                    $imageId = intval($form->get($imageField)->get('asset_data')->getData());
                    $options = [
                        'cropper' => $form->get($imageField)->get('cropper')->getData()
                    ];
                    $assetImage = $this->imageService->copy($entity, $imageField, $imageId, $options, $this->getService()->getImageEntityClass($entity));
                    if (isset($this->events[self::EVENT_IMAGE_HANDLE])) {
                        $this->events[self::EVENT_IMAGE_HANDLE]($entity, $imageField, $assetImage);
                    }
                } elseif ($form->get($imageField)->has('asset_remove') && $form->get($imageField)->get('asset_remove')->getData() === 'remove') {
                    $this->imageService->delete($entity, $imageField);

                    if (isset($this->events[self::EVENT_IMAGE_HANDLE])) {
                        $this->events[self::EVENT_IMAGE_HANDLE]($entity, $imageField, null);
                    }
                }
            }

            $this->doctrine->flush();
        }
    }

    protected function handleFiles(FormInterface $form, object $entity): void
    {
        foreach ($this->getService()->getFileFields($form, $entity) as $fileFieldName) {
            if (!empty($form->get($fileFieldName)->get('asset')->getData())) {
                $fileField = $form->get($fileFieldName)->get('asset')->getData();

                $options = [
                    'context' => $form->get($fileFieldName)->getConfig()->getOptions()['ws']['context'] ?? null
                ];

                $assetFile = $this->fileService->handle($fileField, $entity, $fileFieldName, $options);
                if (isset($this->events[self::EVENT_FILE_HANDLE])) {
                    $this->events[self::EVENT_FILE_HANDLE]($entity, $fileFieldName, $assetFile);
                }
            } elseif (
                $form->get($fileFieldName)->has('asset_remove') &&
                $form->get($fileFieldName)->get('asset_remove')->getData() === 'remove'
            ) {
                $this->fileService->delete($entity, $fileFieldName);

                if (isset($this->events[self::EVENT_FILE_HANDLE])) {
                    $this->events[self::EVENT_FILE_HANDLE]($entity, $fileFieldName, null);
                }
            }

            $this->doctrine->flush();
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

    protected function getFilterExtendedFormType(): ?string
    {
        return null;
    }

    protected function getFilterExtendedForm(): ?FormInterface
    {
        $formType = $this->getFilterExtendedFormType();

        if (null !== $formType) {
            $form = $this->createForm($formType, null, [
                'csrf_protection' => false,
                'method' => 'GET',
                'translation_domain' => $this->getTranslatorPrefix()
            ]);
            return $form;
        }

        return null;
    }

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessAllowed('view');

        $page = (int) $request->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $limit = (int) $request->get('limit', $this->getLimit());
        if (!$limit) {
            $limit = $this->getLimit();
        }

        // Search simple
        $search = (string) $request->get('f');

        // Filter extended
        $filterExtended = $this->getFilterExtendedForm();
        $filterExtendedView = null;
        $filterExtendedData = null;

        if ($filterExtended instanceof Form) {
            $filterExtended->handleRequest($request);
            if ($filterExtended->isSubmitted() && $filterExtended->isValid()) {
                $filterExtendedData = $filterExtended->getData();
            }
            $filterExtendedView = $filterExtended->createView();
        }

        // Retrieve data
        if (isset($this->events[self::EVENT_INDEX_FETCH_DATA])) {
            $data = $this->events[self::EVENT_INDEX_FETCH_DATA]();
        } else {
            $data = $this->getService()->getAll($search, $filterExtendedData, $page, $limit, (string)$request->get('sort'), (string)$request->get('dir'));
        }

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

        // Hook to add data to the view
        $extraData = [];
        if (isset($this->events[self::EVENT_INDEX_EXTRA_DATA])) {
            $extraData = $this->events[self::EVENT_INDEX_EXTRA_DATA]();
        }

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
        /** @var ?object */
        $entity = $this->getService()->getEntity();
        if ($entity === null) {
            throw new BadRequestHttpException($this->translator->trans('bad_request', [], 'ws_cms'));
        }

        if (isset($this->events[self::EVENT_CREATE_NEW_ENTITY])) {
            $this->events[self::EVENT_CREATE_NEW_ENTITY]($entity);
        }

        if (isset($this->events[self::EVENT_CREATE_CREATE_FORM])) {
            $form = $this->events[self::EVENT_CREATE_CREATE_FORM]($entity);
        } else {
            $form = $this->createForm(
                $this->getService()->getFormClass(),
                $entity,
                [
                    'translation_domain' => $this->getTranslatorPrefix()
                ]
            );
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->getService()->create($entity);

                    $this->handleImages($form, $entity);

                    $this->handleFiles($form, $entity);

                    $this->addFlash('cms_success', $this->trans('create_success', [], $this->getTranslatorPrefix()));

                    if ($form->has('saveAndBack') && $form->get('saveAndBack')->isClicked()) {
                        $url = $form->has('referer')
                            ? $form->get('referer')->getData()
                            : $this->redirectToRoute($this->getRouteNamePrefix() . '_index');
                        return $this->redirect($url);
                    }

                    return $this->redirectToRoute($this->getRouteNamePrefix() . '_edit', ['id' => $entity->getId()]);
                } catch (\Exception $e) {
                    $this->addFlash('cms_error', $this->trans('create_error', [], $this->getTranslatorPrefix()));
                }
            } else {
                $this->addFlash('cms_error', $this->getFormErrorMessagesList($form));
            }
        }

        $extraData = [];
        if (isset($this->events[self::EVENT_CREATE_EXTRA_DATA])) {
            $extraData = $this->events[self::EVENT_CREATE_EXTRA_DATA]();
        }

        return $this->render($this->getTemplate('show.html.twig'), array_merge([
            'form' => $form->createView(),
            'isCreate' => true,
            'trans_prefix' => $this->getTranslatorPrefix(),
            'route_prefix' => $this->getRouteNamePrefix(),
        ], $extraData));
    }

    #[Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, int $id): Response
    {
        $this->denyAccessUnlessAllowed('edit');

        $entity = $this->getService()->get($id);
        if ($entity === null || get_class($entity) !== $this->getService()->getEntityClass()) {
            throw new NotFoundHttpException(sprintf($this->trans('not_found', [], $this->getTranslatorPrefix()), $id));
        }

        if (isset($this->events[self::EVENT_EDIT_CREATE_FORM])) {
            $form = $this->events[self::EVENT_EDIT_CREATE_FORM]($entity);
        } else {
            $form = $this->createForm(
                $this->getService()->getFormClass(),
                $entity,
                [
                    'translation_domain' => $this->getTranslatorPrefix()
                ]
            );
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->getService()->edit($entity);

                    $this->handleImages($form, $entity);

                    $this->handleFiles($form, $entity);

                    $this->addFlash('cms_success', $this->trans('edit_success', [], $this->getTranslatorPrefix()));
                    if ($form->has('saveAndBack') && $form->get('saveAndBack')->isClicked()) {
                        $url = $form->has('referer')
                            ? $form->get('referer')->getData()
                            : $this->redirectToRoute($this->getRouteNamePrefix() . '_index');
                        return $this->redirect($url);
                    }

                    return $this->redirectToRoute($this->getRouteNamePrefix() . '_edit', ['id' => $entity->getId()]);
                } catch (\Exception $e) {
                    $this->addFlash('cms_error', $this->trans('edit_error', [], $this->getTranslatorPrefix()));
                }
            } else {
                $this->addFlash('cms_error', $this->getFormErrorMessagesList($form));
            }
        }

        $extraData = [];
        if (isset($this->events[self::EVENT_EDIT_EXTRA_DATA])) {
            $extraData = $this->events[self::EVENT_EDIT_EXTRA_DATA]();
        }

        return $this->render($this->getTemplate('show.html.twig'), array_merge([
            'form' => $form->createView(),
            'isCreate' => false,
            'trans_prefix' => $this->getTranslatorPrefix(),
            'route_prefix' => $this->getRouteNamePrefix(),
        ], $extraData));
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
                ['msg' => $this->translator->trans('bad_request', [], 'ws_cms')],
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
        } catch (\Exception $e) {
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
        } catch (AccessDeniedException $exception) {
            return $this->json(['msg' => $this->trans('not_allowed', [], 'ws_cms')], Response::HTTP_FORBIDDEN);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->json(
                ['msg' => $this->translator->trans('bad_request', [], 'ws_cms')],
                Response::HTTP_BAD_REQUEST
            );
        }

        $params = json_decode((string) $request->getContent(), true);
        if (!isset($params['ids']) || empty($params['ids'])) {
            return $this->json(
                ['msg' => $this->translator->trans('bad_request', [], 'ws_cms')],
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
        $search = (string) $request->get('f');

        // Filter extended
        $filterExtended = $this->getFilterExtendedForm();
        $filterExtendedData = null;

        if ($filterExtended instanceof Form) {
            $filterExtended->handleRequest($request);
            if ($filterExtended->isSubmitted() && $filterExtended->isValid()) {
                $filterExtendedData = $filterExtended->getData();
            }
        }

        // Retrieve data
        if (isset($this->events[self::EVENT_EXPORT_FETCH_DATA])) {
            $data = $this->events[self::EVENT_EXPORT_FETCH_DATA]();
        } else {
            $data = $service->getDataExport($search, $filterExtendedData, (string)$request->get('sort'), (string)$request->get('dir'));
        }

        $format = (string) strtolower($request->get('format', CsvExportProvider::EXPORT_FORMAT));

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
