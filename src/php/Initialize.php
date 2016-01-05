<?php


namespace dbmigrate;


use dbmigrate\application\sql\MigrationTablePresenceConstraint;
use dbmigrate\application\sql\RunMigration;
use dbmigrate\application\sql\SqlFile;

class Initialize
{
    private $migrationTablePresenceConstraint;

    /** @var  RunMigration */
    private $runMigration;

    public function __construct(\PDO $pdo)
    {
        $this->runMigration = new RunMigration($pdo);
        $this->migrationTablePresenceConstraint = new MigrationTablePresenceConstraint($pdo);
    }


    public function __invoke()
    {
        $this->migrationTablePresenceConstraint->assertTableMissing();
        $this->runMigration->run(new SqlFile(new \SplFileInfo(__DIR__ . "/../sql/init.sql")));
        $this->migrationTablePresenceConstraint->assertTablePresent();
    }
}