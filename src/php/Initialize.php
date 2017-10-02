<?php


namespace dbmigrate;


use dbmigrate\application\sql\MigrationTablePresenceConstraint;
use dbmigrate\application\sql\RunMigration;
use dbmigrate\application\sql\SqlFile;
use PDO;

class Initialize
{
    /**
     * @var string
     */
    private $databaseEngine;

    /**
     * @var MigrationTablePresenceConstraint
     */
    private $migrationTablePresenceConstraint;

    /** @var  RunMigration */
    private $runMigration;

    public function __construct(\PDO $pdo)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->databaseEngine = strtolower($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
        $this->runMigration = new RunMigration($pdo);
        $this->migrationTablePresenceConstraint = new MigrationTablePresenceConstraint($pdo);
    }


    public function __invoke()
    {
        $initScript = __DIR__ . "/../sql/init-{$this->databaseEngine}.sql";
        if (!file_exists($initScript)) {
            throw new \Exception("Unsupported database engine: {$this->databaseEngine}. Create a src/sql/init-{$this->databaseEngine}.sql script to support it");
        }

        $this->migrationTablePresenceConstraint->assertTableMissing();
        $this->runMigration->run(new SqlFile(new \SplFileInfo($initScript)));
        $this->migrationTablePresenceConstraint->assertTablePresent();
    }
}
