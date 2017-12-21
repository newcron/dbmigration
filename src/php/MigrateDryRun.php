<?php


namespace dbmigrate;


use dbmigrate\application\MigrationDirectoryValidator;
use dbmigrate\application\MigrationFileScanner;
use dbmigrate\application\Planner;
use dbmigrate\application\sql\LoadMigrations;
use dbmigrate\application\sql\SqlFile;
use PDO;

class MigrateDryRun
{
    /** @var  \PDO */
    private $pdo;

    /** @var  \SplFileInfo */
    private $sqlDirectory;

    public function __construct(\PDO $pdo, \SplFileInfo $sqlDirectory)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        (new MigrationDirectoryValidator())->assertValidMigrationFileDirectory($sqlDirectory);

        $this->pdo = $pdo;
        $this->sqlDirectory = $sqlDirectory;
    }

    /** @return SqlFile[] */
    public function __invoke()
    {
        $fileScanner = new MigrationFileScanner($this->sqlDirectory);
        $loadMigrations = new LoadMigrations($this->pdo);

        return (new Planner($fileScanner, $loadMigrations))->findMigrationsToInstall();

    }
}