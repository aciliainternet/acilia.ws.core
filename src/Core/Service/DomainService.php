<?php

namespace WS\Core\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WS\Core\Entity\Domain;
use WS\Core\Repository\DomainRepository;

final class DomainService implements DomainInterface
{
    protected ?array $domains = null;

    public function __construct(
        protected LoggerInterface $logger,
        protected EntityManagerInterface $em,
        protected DomainRepository $repository
    ) {
    }

    public function getDomains(): array
    {
        if ($this->domains === null) {
            $this->domains = [];
            $domains = $this->repository->getAll();
            /** @var Domain $domain */
            foreach ($domains as $domain) {
                $this->domains['host'][$domain->getHost()][] = $domain;
                $this->domains['id'][$domain->getId()] = $domain;
            }
        }

        return $this->domains;
    }

    public function create(Domain $domain): Domain
    {
        try {
            $this->em->persist($domain);
            $this->em->flush();

            $this->logger->info(sprintf('Created domain ID::%s', $domain->getId()));

            return $domain;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error creating administrator. Error: %s', $e->getMessage()));

            throw $e;
        }
    }

    public function get(int $id): ?Domain
    {
        $domains = $this->getDomains();

        if (isset($domains['id'][$id])) {
            return $domains['id'][$id];
        }

        return $this->repository->find($id);
    }

    public function getByHost(string $host): array
    {
        $domains = $this->getDomains();

        if (isset($domains['host'][$host])) {
            return $domains['host'][$host];
        }

        throw new \Exception(sprintf('Domain with host "%s" is not registered.', $host));
    }

    /**
     * @return Domain[]
     */
    public function getCanonicals(): array
    {
        $canonicals = [];
        $domains = $this->getDomains();
        if (count($domains) === 0) {
            return $canonicals;
        }

        /** @var Domain $domain */
        foreach ($domains['id'] as $domain) {
            if ($domain->isCanonical()) {
                $canonicals[] = $domain;
            }
        }

        usort($canonicals, fn (Domain $d1, Domain $d2) => strcmp($d1->getHost(), $d2->getHost()));

        return $canonicals;
    }

    /**
     * @return Domain[]
     */
    public function getAliases(): array
    {
        $aliases = [];
        $domains = $this->getDomains();

        /** @var Domain $domain */
        foreach ($domains['id'] as $domain) {
            if ($domain->isAlias()) {
                $aliases[] = $domain;
            }
        }

        usort($aliases, fn (Domain $d1, Domain $d2) => strcmp($d1->getHost(), $d2->getHost()));

        return $aliases;
    }
}
