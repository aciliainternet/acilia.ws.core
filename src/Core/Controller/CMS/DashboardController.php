<?php

namespace WS\Core\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route(path: '/', name: 'ws_dashboard')]
    public function index(): Response
    {
        return $this->render('@WSCore/cms/dashboard/index.html.twig');
    }
}
