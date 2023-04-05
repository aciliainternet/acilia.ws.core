<?php

namespace WS\Core\Service\Entity;

use WS\Core\Entity\Navigation;
use WS\Core\Library\CRUD\AbstractService;

class NavigationService extends AbstractService
{
    public function makeDefault(Navigation $navigation)
    {
        $this->resetDefaults();
        
        $navigation->setDefault(true);
        $this->em->persist($navigation);
        $this->em->flush();
    }

    public function resetDefaults()
    {
        $this->repository->resetDefaults();
    }
}
