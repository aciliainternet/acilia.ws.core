<?php

namespace WS\Core\Controller\CMS;

use Symfony\Component\HttpFoundation\JsonResponse;
use WS\Core\Service\Entity\AssetImageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use WS\Core\Service\ImageService;

/**
 * @Route("/asset-image", name="ws_asset_image_")
 */
class AssetImageController
{
    protected AssetImageService $service;
    protected ImageService $imageService;

    public function __construct(AssetImageService $service, ImageService $imageService)
    {
        $this->service = $service;
        $this->imageService = $imageService;
    }

    /**
     * @Route("/list", name="images")
     * @Security("is_granted('ROLE_CMS')", message="not_allowed")
     */
    public function list(Request $request): JsonResponse
    {
        $filter = (string) $request->get('f');

        $page = (int) $request->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $limit = (int) $request->get('limit', $this->getLimit());
        if (!$limit) {
            $limit = $this->getLimit();
        }

        $data = $this->service->getAll($filter, $page, $limit, (string)$request->get('sort'), (string)$request->get('dir'));

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

    /**
     * @Route("/_save_asset_image", name="save_asset_image", methods={"POST"})
     */
    public function save(Request $request): JsonResponse
    {
        if ($request->files->has('asset')) {
            $imageFile = $request->files->get('asset');

            $assetImage = $this->imageService->handleStandalone($imageFile, ['cropper' => []]);

            return new JsonResponse([
                'path' => $this->imageService->getImageUrl($assetImage, 'original'),
                'id' => $assetImage->getId(),
                'name' => $assetImage->getFilename()
            ]);
        }

        return new JsonResponse(['msg' => 'No asset found'], 500);
    }

    protected function getLimit(): int
    {
        return 20;
    }
}
