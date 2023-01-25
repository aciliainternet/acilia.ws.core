<?php

namespace WS\Core;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WS\Core\Library\ActivityLog\ActivityLogCompilerPass;
use WS\Core\Library\Asset\ImageCompilerPass;
use WS\Core\Library\CRUD\CRUDCompilerPass;
use WS\Core\Library\DataCollector\DataCollectorCompilerPass;
use WS\Core\Library\FactoryCollector\FactoryCollectorCompilerPass;
use WS\Core\Library\Navbar\NavbarCompilerPass;
use WS\Core\Library\Setting\SettingCompilerPass;

class WSCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new SettingCompilerPass());
        $container->addCompilerPass(new ImageCompilerPass());
        $container->addCompilerPass(new NavbarCompilerPass());
        $container->addCompilerPass(new FactoryCollectorCompilerPass());
        $container->addCompilerPass(new ActivityLogCompilerPass());
        $container->addCompilerPass(new CRUDCompilerPass());
        $container->addCompilerPass(new DataCollectorCompilerPass());
    }
}
