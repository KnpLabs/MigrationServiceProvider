<?php

namespace Knp\Provider;

use Silex\ServiceProviderInterface;
use Silex\Application;

use Knp\Migration\Manager as MigrationManager;

class MigrationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->before(function() use ($app) {
            $schema    = $app['db']->getSchemaManager()->createSchema();
            $migration = new MigrationManager($app, $schema);

            if (!$migration->hasVersionInfo()) {
                $migration->createVersionInfo();
            }

            if (true === $migration->migrate() && isset($app['twig'])) {
                $app['twig']->addGlobal('migration_infos', $migration->getMigrationInfos());
            }
        });
    }
}
