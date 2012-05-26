# Migrations

This is a simple homebrew schema migration system for silex and doctrine.

## Install

As usual, just include `knplabs/migration-service-provider` in your `composer.json` (don't tell me you don't have one, it's 2012 already), and register the service. You will have to pass the `migration.path` option, which should contain the path to your migrations files:

```php
$app->register(new \Knp\Provider\MigrationServiceProvider(), array(
    'migration.path' => __DIR__.'/../src/Resources/migration'
));
```

## Enough small talk, I want to write migrations!

And I am too lazy to write a comprehensive documentation right now, so you will have to rely on two external resources:

1. [The marketplace's migrations](https://github.com/KnpLabs/marketplace/tree/master/src/Resources/migrations)
2. [The official documentation for Doctrine's DBAL Schema Manager](http://readthedocs.org/docs/doctrine-dbal/en/latest/reference/schema-manager.html)

## Running migrations

There are two ways of running migrations

### Using the `before` handler

If you pass a `migration.register_before_handler` (set to `true`) when registering the service, then a `before` handler will be registered for migration to be run. It means that the migration manager will be run for each hit to your application.

You might want to enable this behavior for development mode, but please don't do that in production!

### Using the `knp:migration:migrate` command

If you installed the console service provider right, you can use the `knp:migration:migrate` command.

## Writing migrations

A migration consist of a single file, holding a migration class. By design, the migration file must be named something like `<version>_<migration_name>Migration.php` and located in `src/Resources/migrations`, and the class `<migration_name>Migration`. For example, if your migration adds a `bar` field to the `foo` table, and is the 5th migration of your schema, you should name your file `05_FooBarMigration.php`, and the class would be named `FooBarMigration`.

In addition to these naming conventions, your migration class must extends `Knp\Migration\AbstractMigration`, which provides a few helping method such as `getVersion` and default implementations for migration methods.

The migration methods consist of 4 methods:

* `schemaUp`
* `schemaDown`
* `appUp`
* `appDown`

The names are pretty self-explanatory. Each `schema*` method is fed a `Doctrine\DBAL\Schema\Schema` instance of which you're expected to work to add, remove or modify fields and/or tables. The `app*` method are given a `Silex\Application` instance, actually your very application. You can see an example of useful `appUp` migration in the marketplace's [CommentMarkdownCacheMigration](https://github.com/knplabs/marketplace/blob/master/src/Resources/migrations/04_CommentMarkdownCacheMigration.php).

## Migration infos

There's one last method you should know about: `getMigrationInfo`. This method should return a self-explanatory description of the migration (it is optional though, and you can skip its implementation). When a migration implementing the `getMigrationInfo` method is run, and if you use twig, a global variable is set in your twig environment containing an array of all run migration informations.

You can then use it with something like that:

```html
      {% if migration_infos is defined %}
        <div class="alert alert-success">
          <p>Some migrations have been run:</p>
          <ul>
          {% for info in migration_infos %}
            <li>{{ info }}</li>
          {% endfor %}
        </div>
      {% endif %}
```