<?php

namespace WS\Core\Command\Administrator;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use WS\Core\Entity\Administrator;
use WS\Core\Service\Entity\AdministratorService;

#[AsCommand(
    name: 'ws:administrator:add-default',
    description: 'Populate the default Administrator for the App',
    hidden: true
)]
class AddDefaultCommand extends Command
{
    public function __construct(
        protected AdministratorService $administratorService,
        protected UserPasswordHasherInterface $passwordHasherService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $generatedPassword = \substr(\str_shuffle(\str_repeat(
            $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ceil(24 / strlen($x))
        )), 1, 24);

        try {
            $administrator = new Administrator();
            $administrator
                ->setName('Widestand Admin')
                ->setActive(true)
                ->setEmail('admin@widestand')
                ->setProfile('ROLE_ADMINISTRATOR')
                ->setPassword($this->passwordHasherService->hashPassword($administrator, $generatedPassword))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setModifiedAt(new \DateTimeImmutable());

            $this->administratorService->create($administrator);

            $io->success(sprintf('You have created the default Administrator. Email is "admin@widestand". Password is "%s"', $generatedPassword));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        return 0;
    }
}
