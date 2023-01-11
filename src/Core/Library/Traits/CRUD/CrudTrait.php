<?php

namespace WS\Core\Library\Traits\CRUD;

use Symfony\Component\Form\FormInterface;

trait CrudTrait
{
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

    protected function createEntity(): ?object
    {
        return $this->getService()->getEntity();
    }

    protected function createEntityForm(object $entity): FormInterface
    {
        return $this->createForm(
            $this->getService()->getFormClass(),
            $entity,
            ['translation_domain' => $this->getTranslatorPrefix()]
        );
    }

    protected function createExtraData(): array
    {
        return [];
    }

    protected function editEntity(int $id): ?object
    {
        return $this->getService()->get($id);
    }

    protected function editEntityForm(object $entity): FormInterface
    {
        return $this->createForm(
            $this->getService()->getFormClass(),
            $entity,
            ['translation_domain' => $this->getTranslatorPrefix()]
        );
    }

    protected function editExtraData(): array
    {
        return [];
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
