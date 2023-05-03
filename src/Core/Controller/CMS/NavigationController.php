<?php 

namespace WS\Core\Controller\CMS;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WS\Core\Entity\Navigation;
use WS\Core\Form\NavigationItemType;
use WS\Core\Library\CRUD\AbstractController;
use WS\Core\Service\Entity\NavigationService as EntityNavigationService;
use WS\Core\Service\NavigationService;

#[Route(path: '/navigation', name: 'ws_navigation_')]
class NavigationController extends AbstractController
{
    public function __construct(EntityNavigationService $service, private NavigationService $navigationService)
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

    protected function useCRUDTemplate(string $template): bool
    {
        if ($template == 'index.html.twig') {
            return false;
        }

        return true;
    }

    #[Route(path: '/configure/{id}', name: 'configure')]
    public function configure(int $id): Response
    {
        /** @var Navigation | null */
        $navigation = $this->service->get($id);
        
        if (null === $navigation) {
            throw new \Exception('Error loading navigation. Navigation not found!');
        }

        if (!($this->service instanceof EntityNavigationService)) {
            throw new \Exception(
                sprintf('Error loading navigation. Service must be of type %s', EntityNavigationService::class)
            );
        }

        $newItemForm = $this->createForm(NavigationItemType::class, null, [
            'translation_domain' => $this->getTranslatorPrefix(),
            'navigation_tree' => $this->service->getNavigationTree($navigation),
            'navigation_entities' => $this->navigationService->getNavigationEntities(),
        ])->createView();

        return $this->render('@WSCore/cms/navigation/configure.html.twig', [
            'navigation' => $navigation,
            'newItemForm' => $newItemForm,
        ]);
    }

    #[Route(path: '/configure/{id}/new-item', name: 'new_item', methods: ['POST'])]
    public function addItem(int $id): Response
    {
        /** @var Navigation | null */
        $navigation = $this->service->get($id);
        
        if (null === $navigation) {
            throw new \Exception("Error loading navigation. Navigation not found!");
        }
        
        $this->addFlash('cms_error', 'TODO!');

        return $this->redirectToRoute('ws_navigation_configure', [ 'id' => $id]);
    }

    #[Route(path: '/make-default/{id}', name: 'make_default', methods: ['POST'])]
    public function makeDefault(int $id): Response
    {
        /** @var Navigation | null */
        $navigation = $this->service->get($id);
        
        if (null === $navigation) {
            throw new \Exception("Error loading navigation. Navigation not found!");
        }

        try {
            if (!($this->service instanceof EntityNavigationService)) {
                throw new \Exception('Error while trying to access the navigation service!');
            }

            $this->service->makeDefault($navigation);
            $this->addFlash('cms_success', $this->trans('options.make_default.success', [], $this->getTranslatorPrefix()));
        } catch (\Throwable) {
            $this->addFlash('cms_error', $this->trans('options.make_default.error', [], $this->getTranslatorPrefix()));
        }

        return $this->redirectToRoute('ws_navigation_index');
    }
}
