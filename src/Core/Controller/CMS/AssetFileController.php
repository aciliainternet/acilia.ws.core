<?php

namespace WS\Core\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WS\Core\Service\FileService;
use WS\Core\Service\StorageService;

#[Route(path: '/asset-file', name: 'ws_asset_file_')]
class AssetFileController extends AbstractController
{
    public function __construct(protected FileService $service)
    {
    }

    #[Route(path: '/_save_asset_file', name: 'save_asset_file', methods: 'POST')]
    public function save(Request $request): JsonResponse
    {
        if ($request->files->has('asset')) {
            $file = $request->files->get('asset');

            $assetFile = $this->service->handleStandalone($file, [
                'context' => StorageService::CONTEXT_PUBLIC
            ]);

            return new JsonResponse([
                'path' => $this->service->getFileUrl($assetFile),
                'id' => $assetFile->getId(),
                'name' => $assetFile->getFilename()
            ]);
        }

        return new JsonResponse(['msg' => 'No asset found'], 500);
    }
}
