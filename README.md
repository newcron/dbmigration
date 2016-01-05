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

call_user_func(new \dbmigrate\Initialize($pdo));
```

##### Running Migrations (on every deployment)

All your DB migrations (which are basically *.sql* Files) will need to be stored in one directory.
When you point dbmigrate to this directory, it will compare all *.sql* files in there with the data from
the *installed_migrations* table and install migrations that are not yet installed.

To determine if a migration has been installed or not, the dbmigrate will compare the filenames (case sensitive!).
If multiple migrations have to be installed at once, the order in which they are installed will be the natural order of the filenames (using [natsort](http://php.net/natsort)).

```php
$pdo = new PDO("mysql:host=yourdbhost;database=yourdb", "youruser", "yourpass");

call_user_func(new \dbmigrate\Migrate($pdo, new \SplFileInfo("/path/to/your/sql/folder")));
```


### Extending/Running Tests

There is a set of Unit and Integration Tests ensuring the basic functionality of dbmigrate stays intact. To run them you will need

* Linux/MacOS
* Docker 1.8 or higher
* PHP 5.6 or higher

Running the tests is just a matter of

```bash
composer install
make tests
```

**Note For Linux Users:** Docker works a bit different on MacOS than on Linux. This is important to know as the IP address of Docker
containers on Mac is dynamic, where it is always 127.0.0.1 on Linux. To have the Makefile work on Linux, just change the occurences
of `docker-machine ip default` to `127.0.0.1`