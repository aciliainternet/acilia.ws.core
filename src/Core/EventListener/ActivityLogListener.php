<?php

namespace WS\Core\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use WS\Core\Entity\Domain;
use WS\Core\Library\ActivityLog\ActivityLogInterface;
use WS\Core\Library\Domain\DomainDependantInterface;
use WS\Core\Service\ActivityLogService;
use WS\Core\Service\ContextInterface;

#[AsEventListener(event: ControllerEvent::class, method: 'onController', priority: 121)]
class ActivityLogListener
{
    public function __construct(
        private LoggerInterface $logger,
        private ContextInterface $context,
        private ActivityLogService $activityLogService,
        private TokenStorageInterface $tokenStorage
    ) {
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if (!$this->activityLogService->isEnabled()) {
            return;
        }

        $entity = $args->getObject();
        $entityName = get_class($entity);

        if (!$this->activityLogService->isSupported($entityName)) {
            return;
        }

        if (!\method_exists($entity, 'getId')) {
            return;
        }

        try {
            // get entity service
            $entityService = $this->activityLogService->getService($entityName);

            // set date of the change
            $activityLogDate = new \DateTime();

            // get entity changed fields
            $changes = $args->getEntityChangeSet();

            // process datetime fields
            foreach ($changes as $field => $value) {
                if (isset($value[0]) && $value[0] instanceof \DateTimeInterface) {
                    $changes[$field][0] = $value[0]->format('Y-m-d H:i:s');
                }
                if (isset($value[1]) && $value[1] instanceof \DateTimeInterface) {
                    $changes[$field][1] = $value[1]->format('Y-m-d H:i:s');
                }
            }

            // discard unneeded changed fields
            if ($entityService->getActivityLogFields() !== null) {
                foreach ($changes as $field => $value) {
                    if (!in_array($field, $entityService->getActivityLogFields())) {
                        unset($changes[$field]);
                    }
                }
            }

            // save the editorial activity log
            $args->getObjectManager()->getConnection()->insert('ws_activity_log', [
                'activity_log_action' => ActivityLogInterface::UPDATE,
                'activity_log_model' => $entityName,
                'activity_log_model_id' => $entity->getId(),
                'activity_log_changes' => json_encode($changes),
                'activity_log_created_at' => $activityLogDate->format('Y-m-d H:i:s'),
                'activity_log_created_by' => $this->getUsername(),
                'activity_log_domain' => $this->getDomainId($entity)
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        if (!$this->activityLogService->isEnabled()) {
            return;
        }

        $entity = $args->getObject();
        $entityName = get_class($entity);

        if (!$this->activityLogService->isSupported($entityName)) {
            return;
        }

        if (!\method_exists($entity, 'getId')) {
            return;
        }

        try {
            // set date of the insert
            $activityLogDate = new \DateTime();

            // save the editorial activity log
            $args->getObjectManager()->getConnection()->insert('ws_activity_log', [
                'activity_log_action' => ActivityLogInterface::CREATE,
                'activity_log_model' => $entityName,
                'activity_log_model_id' => $entity->getId(),
                'activity_log_changes' => json_encode([]),
                'activity_log_created_at' => $activityLogDate->format('Y-m-d H:i:s'),
                'activity_log_created_by' => $this->getUsername(),
                'activity_log_domain' => $this->getDomainId($entity)
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        if (!$this->activityLogService->isEnabled()) {
            return;
        }

        $entity = $args->getObject();
        $entityName = get_class($entity);

        if (!$this->activityLogService->isSupported($entityName)) {
            return;
        }

        if (!\method_exists($entity, 'getId')) {
            return;
        }

        try {
            // set date of the remove
            $activityLogDate = new \DateTime();

            // save the editorial activity log
            $args->getObjectManager()->getConnection()->insert('ws_activity_log', [
                'activity_log_action' => ActivityLogInterface::DELETE,
                'activity_log_model' => $entityName,
                'activity_log_model_id' => $entity->getId(),
                'activity_log_changes' => json_encode([]),
                'activity_log_created_at' => $activityLogDate->format('Y-m-d H:i:s'),
                'activity_log_created_by' => $this->getUsername(),
                'activity_log_domain' => $this->getDomainId($entity)
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function onController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->activityLogService->isEnabled()) {
            return;
        }

        $request = $event->getRequest();
        if (strpos(strval($request->attributes->get('_route')), 'ws_activity_log_index') === 0) {
            throw new NotFoundHttpException();
        }
    }

    private function getUsername(): string
    {
        if ($this->tokenStorage->getToken() instanceof TokenInterface) {
            return $this->tokenStorage->getToken()->getUserIdentifier();
        }

        return 'annon';
    }

    private function getDomainId(object $entity): ?int
    {
        if ($entity instanceof DomainDependantInterface) {
            if ($entity->getDomain() !== null) {
                return $entity->getDomain()->getId();
            }
        }

        if ($this->context->getDomain() instanceof Domain) {
            return $this->context->getDomain()->getId();
        }

        return null;
    }
}
