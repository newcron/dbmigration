# dbmigrate - Database Migration Tool for MySQL

This tool is heavily inspired from [Flyway](http://flywaydb.org/). This tool is helpful
 for CI/CD environments in which changes to the database need to be automated and should ideally be versionized.

Using dbmigrate, you can specify a directory containing *.sql* files, each being treated as one migration. When run,
the framework will ensure that all SQL files will run against the database using natural sorting order of the files.


### Using the Framework
This is a code-only tool, meaning that there is no CLI/Web interface. You operate the tool using PHP code. It works on
top of *PDO*, meaning that you will need to pass it a preconfigured *PDO* instance pointing to your database.


##### Initializing (first time only)

The framework depends on the existence of a *journal* table in your Database, being named *installed_migrations*. The first
time you run the migration tool, you'll need to *Initialize* it (generating that schema).

```php
$pdo = new PDO("mysql:host=yourdbhost;database=yourdb", "youruser", "yourpass");

(new \dbmigrate\Initialize($pdo))->createInstalledMigrationsTable();
```

##### Running Migrations (on every deployment)

All your DB migrations (which are basically *.sql* Files) will need to be stored in one directory.
When you point dbmigrate to this directory, it will compare all *.sql* files in there with the data from
the *installed_migrations* table and install migrations that are not yet installed.

To determine if a migration has been installed or not, the dbmigrate will compare the filenames (case sensitive!).
If multiple migrations have to be installed at once, the order in which they are installed will be the natural order of the filenames (using [natsort](http://php.net/natsort)).

```php
$pdo = new PDO("mysql:host=yourdbhost;database=yourdb", "youruser", "yourpass");

(new \dbmigrate\Migrate($pdo, new \SplFileInfo("/path/to/your/sql/folder")))->runMissingMigrations();
```

