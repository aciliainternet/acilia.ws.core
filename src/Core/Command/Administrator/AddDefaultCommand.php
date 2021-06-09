<?php

namespace WS\Core\Command\Administrator;

use WS\Core\Entity\Administrator;
use WS\Core\Service\Entity\AdministratorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddDefaultCommand extends Command
{
    protected AdministratorService $administratorService;
    protected UserPasswordHasherInterface $passwordHasherService;

    public function __construct(AdministratorService $administratorService, UserPasswordHasherInterface $passwordHasherService)
    {
        $this->administratorService = $administratorService;
        $this->passwordHasherService = $passwordHasherService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ws:administrator:add-default')
            ->setDescription('Populate the default Administrator for the App')
            ->setHidden(true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
