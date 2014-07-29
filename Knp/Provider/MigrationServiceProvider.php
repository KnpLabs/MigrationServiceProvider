<?php

namespace Knp\Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

use Knp\Migration\Manager as MigrationManager;

use Symfony\Component\Finder\Finder;

use Knp\Console\ConsoleEvents;
use Knp\Console\ConsoleEvent;

use Knp\Command\MigrationCommand;

class MigrationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['migration'] = function() use ($app) {
            return new MigrationManager($app['db'], $app, Finder::create()->in($app['migration.path']));
        };

        $app['dispatcher']->addListener(ConsoleEvents::INIT, function(ConsoleEvent $event) {
            $application = $event->getApplication();
            $application->add(new MigrationCommand());
        });

        if (isset($app['migration.register_before_handler']) && $app['migration.register_before_handler']) {
            $this->registerBeforeHandler($app);
        }
    }

    private function registerBeforeHandler($app)
    {
        $app->before(function() use ($app) {
            $manager = $app['migration'];

            if (!$manager->hasVersionInfo()) {
                $manager->createVersionInfo();
            }

            if (true === $manager->migrate() && isset($app['twig'])) {
                $app['twig']->addGlobal('migration_infos', $manager->getMigrationInfos());
            }
        });
    }

    public function boot(Container $app)
    {
    }
}
