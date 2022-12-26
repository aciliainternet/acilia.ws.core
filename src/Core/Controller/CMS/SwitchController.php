<?php

namespace WS\Core\Controller\CMS;

use WS\Core\Entity\Domain;
use WS\Core\Service\DomainService;
use WS\Core\Service\ContextService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SwitchController extends AbstractController
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected DomainService $domainService
    ) {
    }

    #[Route(path: '/switch-domain/{id}', name: 'ws_switch_domain', methods: ['GET'])]
    #[IsGranted('ROLE_CMS', message: 'not_allowed')]
    public function switch(Request $request, string $id): Response
    {
        $domain = $this->domainService->get(intval($id));
        if ($domain instanceof Domain) {
            $session = $request->getSession();
            if ($session !== null) {
                $session->set(ContextService::SESSION_DOMAIN, $domain->getId());

                $this->addFlash('cms_success', $this->translator->trans(
                    'domain_switched',
                    [
                        '%domain%' => $domain->getHost(),
                        '%locale%' => $domain->getLocale(),
                    ],
                    'ws_cms'
                ));

                return $this->redirectToRoute('ws_dashboard');
            }
        }

        throw $this->createNotFoundException();
    }
}
