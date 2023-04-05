<?php 

namespace WS\Core\Controller\CMS;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WS\Core\Library\CRUD\AbstractController;
use WS\Core\Service\Entity\NavigationService;

#[Route(path: '/navigation', name: 'ws_navigation_')]
class NavigationController extends AbstractController
{
    public function __construct(NavigationService $service)
    {
        $this->service = $service;
    }

    protected function getRouteNamePrefix(): string
    {
        return 'ws_navigation';
    }

    protected function getTranslatorPrefix(): string
    {
        return 'ws_cms_navigation';
    }

}
