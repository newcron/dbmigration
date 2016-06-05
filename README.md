# dbmigrate - Database Migration Tool for MySQL

dbmigrate is a simple utility to automate changes to your database (schema and contents). This tool is typically
helpful for automating code deployments or in scenarios, where it is necessary to audit database changes. It's an alternative
to tools like [Doctrine Migrations](http://doctrine-orm.readthedocs.io/projects/doctrine-migrations/en/latest/reference/generating_migrations.html)
that focus on automatically keeping a database in sync with the domain model. If you want to keep the database schema
in your hands and like the simplicity of versioned SQL files, then this tool might be just right for you.

dbmigrate is heavily inspired from [Flyway](http://flywaydb.org/).

### How does it work

All your migration SQL files will be stored in one directory (let's call it the *migrations directory*).
There is no rule for the filename, dbmigrate will find anything ending with *.sql*.

The first thing you will need to do is to run dbmigrate's *initialization* command, which will create a table
*installed_migrations* in your db:

| Field                   | Type                 |
|-------------------------|----------------------|
| id                      | int(10) unsigned     |
| installation_time       | timestamp            |
| migration_file_name     | varchar(255)         |
| migration_file_checksum | varchar(32)          |
| success                 | enum('true','false') |

Once done, every time you run the *migrate* task, it will scan the migrations directory for migrations that
are not yet logged in the *installed_migrations* table, execute them and store success/failure inside that table.

There are some more things regarding the installation:

1. If dbmigrate finds *several* SQL files that are not installed, it will sort them [naturally using natsort](http://php.net/manual/de/function.natsort.php) before running them.
1. If running a migrations results in an SQL error, the result will be stored with *success=false*. Dbmigrate will not allow
   to run any migration if an unsuccessful migration is found in database. (see the notes on resolving errors below).
1. Besides the filename, dbmigrate also stores the checksum of the files content. It will fail if it detects that an already installed migration file has been altered after installation. (see the notes on resolving errors below).


### Usage
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

##### Dry-Running Migrations

If you just want to know if your new migration *would* be going to be installed, you can perform a
dry run. It will not alter your database in any way, but it will tell you which migrations were
going to be installed, if you would run a migration now.

To perform a Dry-Run:

```php
$pdo = new PDO("mysql:host=yourdbhost;database=yourdb", "youruser", "yourpass");

$installedMigrations = call_user_func(new \dbmigrate\MigrateDryRun($pdo, new \SplFileInfo("/path/to/your/sql/folder")));

var_dump($installedMigrations);
```


### Resolving Errors

When doing migrations, there are two things in which dbmigrate purposely breaks. In both
cases it's very likely that the database is out of sync with the migration files and needs
to be manually checked:

1. a migration could not be installed successfully
1. migration file was altered after installation

##### Migration could not be installed successfully

MySQL [does not support transactions over alter tables](http://dev.mysql.com/doc/refman/5.7/en/cannot-roll-back.html) -
so if a migration contains a syntax error, there is no way to roll back (imagine, you've got several alter table statements
in your SQL file, the first one passes, the second one as a syntax error and is therefore ignored)

In these cases, all that dbmigrate knows, is that the DB is *not* in the expected state
and therefore will not proceed doing any further migrations to the database - now and in the future.

To resolve that situation, you'll need to manually review the database and either

* Bring it back into the old state and delete the failed entry from the *installed_migrations* table.
* Finish the migration manually and update the *installed_migrations* record to be *success=true*.

##### Migration File was altered after Installation

When a migration is installed, dbmigrate will store the checksum of the file at the time
of installation. If that file is modified afterwards, dbmigrate will notify this and refus
to do any new migration. Like above the reason for this is that it's unclear if the DB is in a valid
state or not.

There are two ways to resolve that situation:

* Revert the old migration file so that the checksum is the same again
* Review the migration file and the state of the database - if there's no difference, just update the record in *installed_migrations* to the
  new checksum.

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