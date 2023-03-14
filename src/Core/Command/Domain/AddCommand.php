<?php

namespace WS\Core\Command\Domain;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WS\Core\Entity\Domain;
use WS\Core\Service\DomainInterface;

#[AsCommand(
    name: 'ws:domain:add',
    description: 'Add a new Domain into WideStand',
)]
class AddCommand extends Command
{
    public function __construct(protected DomainInterface $domainService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('host', InputArgument::REQUIRED, 'The hostname of the domain')
            ->addArgument('locale', InputArgument::REQUIRED, 'The locale of the domain')
            ->addArgument('type', InputArgument::REQUIRED, 'The type of the domain')
            ->addArgument('culture', InputArgument::REQUIRED, 'The culture of the domain')
            ->addArgument('timezone', InputArgument::REQUIRED, 'The timezone of the domain')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $host = $input->getArgument('host');
            $locale = $input->getArgument('locale');
            $type = $input->getArgument('type');
            $culture = $input->getArgument('culture');
            $timezone = $input->getArgument('timezone');
            if ($host === null || $locale === null || $type === null || $culture === null || $timezone === null) {
                return 1;
            }

            if (is_array($host)) {
                $host = $host[0];
            }
            if (is_array($locale)) {
                $locale = $locale[0];
            }
            if (is_array($type)) {
                $type = $type[0];
            }
            if (is_array($culture)) {
                $culture = $culture[0];
            }
            if (is_array($timezone)) {
                $timezone = $timezone[0];
            }

            $domain = (new Domain())
                ->setHost($host)
                ->setLocale($locale)
                ->setType($type)
                ->setCulture($culture)
                ->setTimezone($timezone);

            $this->domainService->create($domain);

            $io->success(sprintf('You have created a new domain: %s (%s)', $host, $locale));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
