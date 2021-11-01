<?php

namespace WS\Core\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WS\Core\Entity\ActivityLog;
use WS\Core\Library\ActivityLog\ActivityLogChanges;
use WS\Core\Repository\ActivityLogRepository;
use WS\Core\Service\ContextService;

class ActivityLogService
{
    protected LoggerInterface $logger;
    protected EntityManagerInterface $em;
    protected ContextService $contextService;
    protected ActivityLogRepository $repository;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, ContextService $contextService)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->contextService = $contextService;
        $this->repository = $this->em->getRepository(ActivityLog::class);
    }

    public function getAll(array $filters, int $page, int $limit): array
    {
        /** @var ActivityLog[] */
        $entities = $this->repository->getAll($this->contextService->getDomain(), $filters, $limit, ($page - 1) * $limit);

        $total = $this->repository->getAllCount($this->contextService->getDomain(), $filters);

        /** @var ActivityLog $log */
        foreach ($entities as $log) {
            $log->setParsedChanges($this->getParsedChanges($log));
        }

        return [
            'data' => $entities,
            'total' => $total
        ];
    }

    private function getParsedChanges(ActivityLog $log): array
    {
        $changes = $log->getChanges();

        $result = [];
        foreach ($changes as $key => $value) {
            // sensible value types
            if (\preg_match('/password/', \strtolower($key))) {
                $result[$key] = new ActivityLogChanges($this->parseKeyName($key), '*******', '*******');

                break;
            }

            // base value types
            if (! is_array($value[0]) && ! is_array($value[1])) {
                $result[$key] = new ActivityLogChanges($this->parseKeyName($key), $value[0], $value[1]);

                break;
            }

            // array value types
            if (\is_array($value[0]) && \is_array($value[1])) {
                foreach(array_merge(array_keys($value[0]), array_keys($value[1])) as $subKey) {

                    if (! $this->isFieldValid($subKey)) {
                        continue;
                    }

                    if (! $this->isFieldChanged($subKey, $value[0], $value[1])) {
                        continue;
                    }

                    $beforeValue = $value[0][$subKey] ?? '-';
                    $afterValue = $value[1][$subKey] ?? '-';

                    if (\is_array($beforeValue) && isset($beforeValue['date'])) {
                        $beforeValue = $beforeValue['date'];
                    }

                    if (\is_array($afterValue) && isset($afterValue['date'])) {
                        $afterValue = $afterValue['date'];
                    }

                    $newKey = \sprintf('%s::%s', $key, $subKey);
                    $result[$newKey] = new ActivityLogChanges(
                        \sprintf('%s :: %s', $this->parseKeyName($key), $this->parseKeyName($subKey)),
                        $beforeValue,
                        $afterValue
                    );
                }
            }
        }

        return $result;
    }

    public function getUsers(): array
    {
        return $this->repository->getAllUsers();
    }

    public function getModels(): array
    {
        return $this->repository->getAllModels();
    }

    protected function parseKeyName(string $key): string
    {
        return \ucfirst(\strval(\preg_replace('/(?<!\ )[A-Z]/', ' $0', $key)));
    }

    protected function isFieldChanged(string $key, array $before, array $after): bool
    {
        if (isset($before[$key]) && isset($after[$key]) && $before[$key] === $after[$key]) {
            return false;
        }

        if (! isset($before[$key]) && $after[$key] === null) {
            return false;
        }

        if (! isset($after[$key]) && $before[$key] === null) {
            return false;
        }

        return true;
    }

    protected function isFieldValid(string $key): bool
    {
        // @todo: implement relationships
        if (in_array($key, ['__initializer__', '__cloner__', '__isInitialized__'])) {
            return false;
        }

        return true;
    }

}
