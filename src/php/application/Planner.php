<?php


namespace dbmigrate\application;


use dbmigrate\application\schema\InstalledMigrations;
use dbmigrate\application\sql\LoadMigrations;
use dbmigrate\application\sql\SqlFile;

class Planner
{
    /** @var  MigrationFileScanner */
    private $migrationFileScanner;

    /** @var  LoadMigrations */
    private $migrationLoader;

    /**
     * Planner constructor.
     * @param MigrationFileScanner $migrationFileScanner
     * @param LoadMigrations $migrationLoader
     */
    public function __construct(MigrationFileScanner $migrationFileScanner, LoadMigrations $migrationLoader)
    {
        $this->migrationFileScanner = $migrationFileScanner;
        $this->migrationLoader = $migrationLoader;
    }

    /** @return SqlFile[] */
    public function findMigrationsToInstall()
    {
        $migrationsInFilesystem = $this->migrationFileScanner->listSqlFiles();
        $installedMigrations = new InstalledMigrations($this->migrationLoader->allInstalledMigrations());

        return array_filter($migrationsInFilesystem, [$installedMigrations, "needsToBeInstalled"]);
    }


}