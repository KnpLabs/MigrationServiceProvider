<?php

namespace Knp\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

use Knp\Migration\Manager as MigrationManager;

use Symfony\Component\Finder\Finder;

use Knp\Console\Events as ConsoleEvents;
use Knp\Console\Event as ConsoleEvent;

use Knp\Command\MigrationCommand;

class MigrationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['migration'] = $app->share(function() use ($app) {
            return new MigrationManager($app['db'], $app, Finder::create()->in($app['migration.path']));
        });

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

    public function boot(Application $app)
    {        
    }
}
