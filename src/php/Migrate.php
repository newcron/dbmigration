<?php


namespace dbmigrate;


use dbmigrate\application\MigrationException;
use dbmigrate\application\MigrationFileScanner;
use dbmigrate\application\Planner;
use dbmigrate\application\schema\Migration;
use dbmigrate\application\sql\LoadMigrations;
use dbmigrate\application\sql\LogMigration;
use dbmigrate\application\sql\RunMigration;

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
        if($pdo === null) {
            throw new \InvalidArgumentException("PDO may not be null");
        }
        if($sqlDirectory===null) {
            throw new \InvalidArgumentException("sqlDirectory may not be null");
        }

        if(!$sqlDirectory->isDir() || !is_readable($sqlDirectory->getPathname())) {
            throw new \InvalidArgumentException("sqlDirectory ".$sqlDirectory->getPathname()." does not exist or is not readable");
        }
        $this->pdo = $pdo;
        $this->sqlDirectory = $sqlDirectory;
    }

    public function runMissingMigrations() {
        $fileScanner = new MigrationFileScanner($this->sqlDirectory);
        $loadMigrations = new LoadMigrations($this->pdo);
        $migrationsToInstall = (new Planner($fileScanner, $loadMigrations))->findMigrationsToInstall();

        $runMigration = new RunMigration($this->pdo);
        $logMigration = new LogMigration($this->pdo);
        foreach ($migrationsToInstall as $migration) {
            try {
                $runMigration->run($migration);
            } catch(\Exception $e) {
                $logMigration->logFailure($migration);
                throw new MigrationException("Could not Execute Migration ".$migration->getFile()->getPathname(), $e);
            }
            $logMigration->logSuccess($migration);
        }

    }


}