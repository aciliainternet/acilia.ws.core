<?php

namespace WS\Core\Library\CRUD;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use WS\Core\Library\Asset\Form\AssetFileType;
use WS\Core\Library\Asset\Form\AssetImageType;
use WS\Core\Library\DBLogger\DBLoggerInterface;
use WS\Core\Library\Domain\DomainDependantInterface;
use WS\Core\Service\ContextInterface;

abstract class AbstractService implements DBLoggerInterface
{
    protected AbstractRepository $repository;

    public function __construct(
        protected LoggerInterface $logger,
        protected EntityManagerInterface $em,
        protected ContextInterface $context
    ) {
        /** @var AbstractRepository */
        $repository = $this->em->getRepository($this->getEntityClass());
        $this->repository = $repository;
    }

    public function getEntityClass(): string
    {
        $serviceClass = get_class($this);
        $classPath = explode('\\', $serviceClass);

        $serviceNamespacePrefix = $classPath[0];
        if ($serviceNamespacePrefix === 'WS') {
            $serviceClassname = str_replace('Service', '', $classPath[4]);
            return sprintf('WS\Core\Entity\%s', $serviceClassname);

        } elseif ($serviceNamespacePrefix === 'App') {
            $serviceClassname = str_replace('Service', '', $classPath[3]);
            return sprintf('App\Entity\%s', $serviceClassname);

        } else {
            throw new \Exception(sprintf('Unable to find class for Service: %s', $serviceClass));
        }
    }

    abstract public function getSortFields(): array;

    abstract public function getListFields(): array;

    abstract public function getFilterFields(): array;

    public function getImageEntityClass(object $entity): ?string
    {
        return null;
    }

    public function getImageFields(FormInterface $form, object $entity): array
    {
        $images = [];

        foreach ($form as $field) {
            if ($field->getConfig()->getType()->getInnerType() instanceof AssetImageType) {
                $images[] = (string) $field->getPropertyPath();
            }
        }

        return $images;
    }

    public function getFileFields(FormInterface $form, object $entity): array
    {
        $files = [];

        foreach ($form as $field) {
            if ($field->getConfig()->getType()->getInnerType() instanceof AssetFileType) {
                $files[] = (string) $field->getPropertyPath();
            }
        }

        return $files;
    }

    public function getEntity(): ?object
    {
        try {
            if (!class_exists($this->getEntityClass())) {
                return null;
            }

            $ref = new \ReflectionClass($this->getEntityClass());

            return $ref->newInstance();
        } catch (\ReflectionException) {
            return null;
        }
    }

    public function get(int $id): ?object
    {
        return $this->repository->find($id);
    }

    public function getAll(
        ?string $search,
        ?array $filter,
        int $page,
        int $limit,
        string $sort = '',
        string $dir = ''
    ): array {
        if ($sort) {
            $sortFields = [...array_keys($this->getSortFields()), ...array_values($this->getSortFields())];
            if (!in_array($sort, $sortFields)) {
                throw new \Exception('Sort by this field is not allowed');
            }
            $orderBy = [(string) $sort => $dir ? strtoupper($dir) : 'ASC'];
        } else {
            $orderBy = ['id' => 'DESC'];
            if (!empty($this->getSortFields())) {
                $sortFields = array_slice($this->getSortFields(), 0, 1);
                if (key($sortFields) === 0) {
                    $orderBy = [$this->getSortFields()[0] => 'DESC'];
                } else {
                    $orderBy = [key($sortFields) => $sortFields[key($sortFields)]];
                }
            }
        }

        $filterFields = $this->getFilterFields();

        $entities = $this->repository->getAll($this->context->getDomain(), $search, $filter, $filterFields, $orderBy, $limit, ($page - 1) * $limit);
        $total = $this->repository->getAllCount($this->context->getDomain(), $search, $filter, $filterFields);

        return ['total' => $total, 'data' => $entities];
    }

    public function create(object $entity): object
    {
        if (get_class($entity) !== $this->getEntityClass()) {
            throw new \Exception(sprintf('This service only handles "%s" but "%s" was provided.', $this->getEntityClass(), get_class($entity)));
        }

        if (!\method_exists($entity, 'getId')) {
            throw new \Exception('This service only handles WS inherited entities.');
        }

        try {
            if ($entity instanceof DomainDependantInterface) {
                $entity->setDomain($this->context->getDomain());
            }

            $this->em->persist($entity);
            $this->em->flush();

            $this->logger->info(sprintf('Created %s ID::%s', $this->getEntityClass(), $entity->getId()));

            return $entity;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error creating %s. Error: %s', $this->getEntityClass(), $e->getMessage()));

            throw $e;
        }
    }

    public function edit(object $entity): object
    {
        if (get_class($entity) !== $this->getEntityClass()) {
            throw new \Exception(sprintf('This service only handles "%s" but "%s" was provided.', $this->getEntityClass(), get_class($entity)));
        }

        if (!\method_exists($entity, 'getId')) {
            throw new \Exception('This service only handles WS inherited entities.');
        }

        try {
            $this->em->flush();

            $this->logger->info(sprintf('Edited %s ID::%s', $this->getEntityClass(), $entity->getId()));

            return $entity;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error editing %s ID::%s. Error: %s', $this->getEntityClass(), $entity->getId(), $e->getMessage()));

            throw $e;
        }
    }

    public function delete(object $entity): void
    {
        if (get_class($entity) !== $this->getEntityClass()) {
            throw new \Exception(sprintf('This service only handles "%s" but "%s" was provided.', $this->getEntityClass(), get_class($entity)));
        }

        if (!\method_exists($entity, 'getId')) {
            throw new \Exception('This service only handles WS inherited entities.');
        }

        $id = $entity->getId();
        try {
            $this->em->remove($entity);
            $this->em->flush();

            $this->logger->info(sprintf('Removed %s ID::%s', $this->getEntityClass(), $id));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error removing %s ID::%s. Error: %s', $this->getEntityClass(), $id, $e->getMessage()));

            throw $e;
        }
    }

    public function batchDelete(array $ids): void
    {
        try {
            $this->repository->batchDelete($ids);

            $this->logger->info(
                sprintf('Batch delete applied to %s. IDs: %s', $this->getEntityClass(), implode(', ', $ids))
            );
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Error applying a batch delete to %s. IDs: %s. Error: %s',
                    $this->getEntityClass(),
                    implode(', ', $ids),
                    $e->getMessage()
                )
            );

            throw $e;
        }
    }
}
