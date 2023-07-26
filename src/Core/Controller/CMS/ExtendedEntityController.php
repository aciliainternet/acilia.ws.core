<?php

namespace WS\Core\Controller\CMS;

use WS\Core\Library\CRUD\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route(path: '/extended_entity', name: 'ws_extended_entity_')]
class ExtendedEntityController extends AbstractController
{
    public function __construct(
    ) {
    }

    #[Route(path: '/{entityName}', name: 'modal')]
    #[IsGranted('ROLE_CMS', message: 'not_allowed')]
    public function modal(Request $request, string $entityName): Response
    {
        if ($request->request->get('entityPath')) {
            $entityPath = $request->request->get('entityPath');
        } else {
            $entityPath = 'App/CMS/';
        }
        $entityPath = sprintf('%s%s', $entityPath, $entityName);
        $entity = new $entityPath();
        if ($entity === null) {
            throw new BadRequestHttpException($this->trans('bad_request', [], 'ws_cms'));
        }

        // Create entity Form
        $form = $this->createEntityForm($entity);

        return $this->render(
            $this->getTemplate('modal.html.twig'),
            [
                'form' => $form->createView(),
                'isCreate' => true,
                'trans_prefix' => $this->getTranslatorPrefix(),
                'route_prefix' => $this->getRouteNamePrefix(),
            ]
        );
    }


}