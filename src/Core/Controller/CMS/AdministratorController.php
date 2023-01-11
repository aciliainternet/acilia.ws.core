<?php

namespace WS\Core\Controller\CMS;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WS\Core\Form\AdministratorProfileType;
use WS\Core\Library\CRUD\AbstractController;
use WS\Core\Service\Entity\AdministratorService;

#[Route(path: '/administrator', name: 'ws_administrator_')]
class AdministratorController extends AbstractController
{
    public function __construct(AdministratorService $service)
    {
        $this->service = $service;
    }

    protected function getRouteNamePrefix(): string
    {
        return 'ws_administrator';
    }

    protected function getTranslatorPrefix(): string
    {
        return 'ws_cms_administrator';
    }

    #[Route(path: '/profile', name: 'profile')]
    #[IsGranted('ROLE_CMS', message: 'not_allowed')]
    public function profile(Request $request): Response
    {
        /** @var object */
        $administrator = $this->getUser();

        $form = $this->createForm(
            AdministratorProfileType::class,
            $administrator,
            [
                'translation_domain' => $this->getTranslatorPrefix()
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->service->edit($administrator);

                    $this->addFlash('cms_success', $this->trans('profile.edit_success', [], $this->getTranslatorPrefix()));
                } catch (\Exception $e) {
                    $this->addFlash('cms_error', $this->trans('profile.edit_error', [], $this->getTranslatorPrefix()));
                }
            } else {
                $this->addFlash('cms_error', $this->getFormErrorMessagesList($form));
            }
        }

        return $this->render('@WSCore/cms/administrator/profile.html.twig', ['form' => $form]);
    }

    protected function editEntityForm(object $entity): FormInterface
    {
        return $this->createForm(
            $this->getService()->getFormClass(),
            $entity,
            [
                'edit' => true,
                'translation_domain' => $this->getTranslatorPrefix()
            ]
        );
    }
}
