# Migrations

This is a simple homebrew schema migration system. Since there is no CLI system in Silex, everything takes place during the `before` filter. A migration consist of a single file, holding a migration class. By design, the migration file must be named something like `<version>_<migration_name>Migration.php` and located in `src/Resources/migrations`, and the class `<migration_name>Migration`. For example, if your migration adds a `bar` field to the `foo` table, and is the 5th migration of your schema, you should name your file `05_FooBarMigration.php`, and the class would be named `FooBarMigration`.

In addition to these naming conventions, your migration class must extends `Knp\Migration\AbstractMigration`, which provides a few helping method such as `getVersion` and default implementations for migration methods.

The migration methods consist of 4 methods:

* `schemaUp`
* `schemaDown`
* `appUp`
* `appDown`

The names are pretty self-explanatory. Each `schema*` method is fed a `Doctrine\DBAL\Schema\Schema` instance of which you're expected to work to add, remove or modify fields and/or tables. The `app*` method are given a `Silex\Application` instance, actually your very application. You can see an example of useful `appUp` migration in the marketplace's [CommentMarkdownCacheMigration](https://github.com/knplabs/marketplace/blob/master/src/Resources/migrations/04_CommentMarkdownCacheMigration.php).

There's one last method you should know about: `getMigrationInfo`. This method should return a self-explanatory description of the migration (it is optional though, and you can skip its implementation).