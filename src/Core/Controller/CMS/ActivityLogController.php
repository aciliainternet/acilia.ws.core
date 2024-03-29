<?php

namespace WS\Core\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Service\Entity\ActivityLogService;

#[Route(path: '/activity-log', name: 'ws_activity_log_')]
class ActivityLogController extends AbstractController
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected ActivityLogService $service
    ) {
    }

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        if (!$this->isGranted('ROLE_WS_CORE_ACTIVITY_LOG')) {
            throw $this->createAccessDeniedException($this->translator->trans('not_allowed', [], 'ws_cms'));
        }

        $page = intval($request->get('page', 1));
        if ($page < 1) {
            $page = 1;
        }

        $limit = intval($request->get('limit', 20));
        if (!$limit) {
            $limit = 20;
        }

        $models = $this->service->getModels();
        $filters = [];

        $modelIdFilter = (int) $request->query->get('f');
        $userFilter = (string) $request->query->get('u');
        $modelFilter = (string) $request->query->get('m');
        if ($modelIdFilter > 0 && is_int($modelIdFilter)) {
            $filters['model_id'] = $modelIdFilter;
        }
        if ($userFilter !== '') {
            $filters['user'] = $userFilter;
        }
        if ($modelFilter !== '' && in_array($modelFilter, array_map(function ($element) {
            return $element['model'];
        }, $models))) {
            $filters['model'] = $modelFilter;
        }

        $data = $this->service->getAll($filters, $page, $limit);

        $paginationData = [
            'currentPage' => $page,
            'url' => $request->get('_route'),
            'nbPages' => ceil($data['total'] / $limit),
            'currentCount' => count($data['data']),
            'totalCount' => $data['total'],
            'limit' => $limit
        ];

        return $this->render(
            '@WSCore/cms/activitylog/index.html.twig',
            array_merge(
                $data,
                [
                    'paginationData' => $paginationData,
                    'params' => $request->query->all(),
                    'filters' => $filters,
                    'trans_prefix' => 'ws_cms_activity_log',
                    'models' => $models
                ]
            )
        );
    }
}
