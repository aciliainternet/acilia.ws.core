<?php

namespace WS\Core\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use WS\Core\Service\ContextInterface;
use WS\Core\Service\TranslationService;

#[Route(path: '/translation', name: 'ws_translation_')]
#[IsGranted('ROLE_WS_CORE_TRANSLATION', message: 'not_allowed')]
class TranslationController extends AbstractController
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected ContextInterface $context,
        protected TranslationService $translationService
    ) {
    }

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $translations = $this->translationService->getForCMS();

        return $this->render('@WSCore/cms/translation/index.html.twig', [
            'domain' => $this->context->getDomain(),
            'translations' => $translations
        ]);
    }

    #[Route(path: '/save', name: 'save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(
                ['msg' => $this->translator->trans('bad_request', [], 'ws_cms')],
                Response::HTTP_BAD_REQUEST
            );
        }
        /** @var array */
        $translations = json_decode((string) $request->getContent(), true);

        try {
            $this->translationService->updateTranslations($translations);
            return $this->json(
                ['msg' => $this->translator->trans('save_success', [], 'ws_cms_translation')],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
        }

        return $this->json(
            ['msg' => $this->translator->trans('save_error', [], 'ws_cms_translation')],
            Response::HTTP_BAD_REQUEST
        );
    }
}
