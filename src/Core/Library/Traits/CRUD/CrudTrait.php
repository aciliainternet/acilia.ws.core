<?php

namespace WS\Core\Library\Traits\CRUD;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait CrudTrait
{
    protected function preIndexFetchData(Request $request): void
    {
    }

    protected function indexFetchData(
        ?string $search,
        ?array $filter,
        int $page,
        int $limit,
        string $sort = '',
        string $dir = ''
    ): array {
        return $this->getService()->getAll($search, $filter, $page, $limit, $sort, $dir);
    }

    protected function indexExtraData(): array
    {
        return [];
    }

    protected function createEntity(Request $request): ?object
    {
        return $this->getService()->getEntity();
    }

    protected function createEntityForm(object $entity): FormInterface
    {
        return $this->createForm(
            $this->getFormClass(),
            $entity,
            ['translation_domain' => $this->getTranslatorPrefix()]
        );
    }

    protected function createExtraData(): array
    {
        return [];
    }

    protected function editEntity(Request $request, mixed $id): ?object
    {
        return $this->getService()->get($id);
    }

    protected function editEntityForm(object $entity): FormInterface
    {
        return $this->createForm(
            $this->getFormClass(),
            $entity,
            ['translation_domain' => $this->getTranslatorPrefix()]
        );
    }

    protected function editExtraData(): array
    {
        return [];
    }

    protected function getFormClass(): string
    {
        $serviceClass = get_class($this);
        $classPath = explode('\\', $serviceClass);

        if ($classPath[0] === 'WS') {
            $controllerClassname = str_replace('Controller', '', $classPath[4]);
            return sprintf('WS\Core\Form\%sType', $controllerClassname);
        } elseif ($classPath[0] === 'App') {
            $controllerClassname = str_replace('Controller', '', $classPath[3]);
            return sprintf('App\Form\CMS\%sType', $controllerClassname);
        } else {
            throw new \Exception(sprintf('Unable to find form for Service: %s', $serviceClass));
        }
    }

    protected function getFilterExtendedFormType(): ?string
    {
        return null;
    }

    protected function getFilterExtendedForm(): ?FormInterface
    {
        $formType = $this->getFilterExtendedFormType();
        if (null === $formType) {
            return null;
        }

        return $this->createForm($formType, null, [
            'csrf_protection' => false,
            'method' => 'GET',
            'translation_domain' => $this->getTranslatorPrefix()
        ]);
    }

    protected function getBatchActions(): array
    {
        return [];
    }

    protected function getLimit(): int
    {
        return 20;
    }
}
