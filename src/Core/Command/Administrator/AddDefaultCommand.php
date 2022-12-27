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

        try {
            $administrator = new Administrator();
            $administrator->setName('Acilia');
            $administrator->setActive(true);
            $administrator->setEmail('info@acilia.es');
            $administrator->setProfile('ROLE_ADMINISTRATOR');
            $administrator->setPassword($this->passwordHasherService->hashPassword($administrator, 'uMZuPuAP2n3y66DT'));

            $this->administratorService->create($administrator);

            $io->success('You have created the default Administrator. Password should be requested!');
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        return 0;
    }
}
