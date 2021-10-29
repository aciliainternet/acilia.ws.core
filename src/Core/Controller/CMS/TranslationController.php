<?php

namespace WS\Core\Controller\CMS;

use WS\Core\Service\ContextService;
use WS\Core\Service\TranslationService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/translation", name="ws_translation_")
 * @Security("is_granted('ROLE_WS_CORE_TRANSLATION')", message="not_allowed")
 */
class TranslationController extends AbstractController
{
    protected TranslatorInterface $translator;
    protected ContextService $contextService;
    protected TranslationService $translationService;

    public function __construct(
        TranslatorInterface $translator,
        ContextService $contextService,
        TranslationService $translationService
    ) {
        $this->translator = $translator;
        $this->translationService = $translationService;
        $this->contextService = $contextService;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $translations = $this->translationService->getForCMS();

        return $this->render('@WSCore/cms/translation/index.html.twig', [
            'domain' => $this->contextService->getDomain(),
            'translations' => $translations
        ]);
    }

    /**
     * @Route("/save", name="save", methods={"POST"})
     */
    public function save(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(
                ['msg' => $this->translator->trans('bad_request', [], 'ws_cms')],
                Response::HTTP_BAD_REQUEST
            );
        }

        $translations = json_decode((string) $request->getContent(), true);

        try {
            $this->translationService->updateTranslations($translations);
            return $this->json(
                ['msg'=> $this->translator->trans('save_success', [], 'ws_cms_translation')],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
        }

        return $this->json(
            ['msg'=> $this->translator->trans('save_error', [], 'ws_cms_translation')],
            Response::HTTP_BAD_REQUEST
        );
    }
}
