<?php

namespace WS\Core\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WS\Core\Service\Entity\AssetImageService;
use WS\Core\Service\ImageService;

#[Route(path: '/asset-image', name: 'ws_asset_image_')]
class AssetImageController extends AbstractController
{
    public function __construct(
        protected AssetImageService $service,
        protected ImageService $imageService
    ) {
    }

    protected function getService(): AssetImageService
    {
        return $this->service;
    }

    protected function getLimit(): int
    {
        return 20;
    }

    #[Route(path: '/list', name: 'images')]
    #[IsGranted('ROLE_CMS', message: 'not_allowed')]
    public function list(Request $request): JsonResponse
    {
        $filter = strval($request->get('f'));

        $page = intval($request->get('page', 1));
        if ($page < 1) {
            $page = 1;
        }

        $limit = intval($request->get('limit', $this->getLimit()));
        if (!$limit) {
            $limit = $this->getLimit();
        }

        $data = $this->getService()->getAll($filter, $page, $limit, strval($request->get('sort')), strval($request->get('dir')));

        $response = [];
        foreach ($data as $image) {
            $response[] = [
                'id' => $image->getid(),
                'name' => $image->getFilename(),
                'image' => $this->imageService->getImageUrl($image, 'original'),
                'thumb' => $this->imageService->getImageUrl($image, 'thumb'),
                'alt' => $image->getFilename(),
            ];
        }

        return new JsonResponse($response);
    }

    #[Route(path: '/_save_asset_image', name: 'save_asset_image', methods: 'POST')]
    public function save(Request $request): JsonResponse
    {
        if ($request->files->has('asset')) {
            /** @var UploadedFile */
            $imageFile = $request->files->get('asset');
            $renditions = json_decode($request->get('renditions', null));
            $assetImage = $this->imageService->handleStandalone($imageFile, ['cropper' => [], 'renditions' => $renditions ]);

            $response = [
                'path' => $this->imageService->getImageUrl($assetImage, 'original'),
                'id' => $assetImage->getId(),
                'name' => $assetImage->getFilename(),
                'mimeType' => $assetImage->getMimeType()
            ];

            if ($renditions !== null) {
                $response['renditions'] = [];
                foreach ($renditions as $r) {
                    $response['renditions'][$r->name] = $this->imageService->getImageUrl($assetImage, $r->name);
                }
            }

            return new JsonResponse($response);
        }

        return new JsonResponse(['msg' => 'No asset found'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route(path: '/soft-delete', name: 'soft_delete', methods: 'POST')]
    #[IsGranted('ROLE_CMS', message: 'not_allowed')]
    public function delete(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(
                ['msg' => 'Bad request'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            /** @var array */
            $params = json_decode(\strval($request->getContent()), true);

            $entity = $this->getService()->get($params['assetId']);
            if ($entity === null) {
                return $this->json([
                    'msg' => 'AssetImage not found'
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            $entity->setVisible(false);
            $this->getService()->edit($entity);

            return $this->json(
                ['msg' => 'Delete success'],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            return $this->json([
                'msg' => 'Asset Image deletion failed'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
