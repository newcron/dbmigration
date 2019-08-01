<?php


namespace dbmigrate;


use dbmigrate\application\MigrationDirectoryValidator;
use dbmigrate\application\MigrationException;
use dbmigrate\application\MigrationFileScanner;
use dbmigrate\application\Planner;
use dbmigrate\application\sql\LoadMigrations;
use dbmigrate\application\sql\LogMigration;
use dbmigrate\application\sql\RunMigration;
use dbmigrate\application\sql\QuerySplitter;
use PDO;

class Migrate
{
    /** @var  \PDO */
    private $pdo;

    /** @var  \SplFileInfo */
    private $sqlDirectory;

    /**
     * Migrate constructor.
     * @param \PDO $pdo
     * @param \SplFileInfo $sqlDirectory
     */
    public function __construct(\PDO $pdo, \SplFileInfo $sqlDirectory)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        (new MigrationDirectoryValidator())->assertValidMigrationFileDirectory($sqlDirectory);

        $this->pdo = $pdo;
        $this->sqlDirectory = $sqlDirectory;
    }

    public function __invoke() {
        $fileScanner = new MigrationFileScanner($this->sqlDirectory);
        $loadMigrations = new LoadMigrations($this->pdo);
        $migrationsToInstall = (new Planner($fileScanner, $loadMigrations))->findMigrationsToInstall();

        $runMigration = new RunMigration($this->pdo, new QuerySplitter());
        $logMigration = new LogMigration($this->pdo);
        foreach ($migrationsToInstall as $migration) {
            try {
                $runMigration->run($migration);
                $logMigration->logSuccess($migration);
            } catch(\Exception $e) {
                throw new MigrationException("Could not Execute Migration ".$migration->getFile()->getPathname(), $e);
            }
        }
    }


}