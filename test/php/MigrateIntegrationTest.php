<?php


namespace dbmigrate;


use dbmigrate\application\schema\Migration;
use dbmigrate\application\sql\LoadMigrations;
use dbmigrate\testutil\IntegrationDatabase;

class MigrateTest extends \PHPUnit_Framework_TestCase
{
    use IntegrationDatabase;


    public function testInitializeFromEmptySchema()
    {
        $pdo = $this->aNewInitializedSchema();

        (new Migrate($pdo, new \SplFileInfo(__DIR__ . "/../testsets/nummericsorting")))->runMissingMigrations();

        $migrations = (new LoadMigrations($pdo))->allInstalledMigrations();
        $this->assertEquals("v3_test.sql", $migrations[0]->getFilename());
        $this->assertEquals("v10_test.sql", $migrations[1]->getFilename());
    }

    public function testInitializeIncremental()
    {

        $pdo = $this->aNewInitializedSchema();

        (new Migrate($pdo, new \SplFileInfo(__DIR__ . "/../testsets/incremental/step1")))->runMissingMigrations();
        (new Migrate($pdo, new \SplFileInfo(__DIR__ . "/../testsets/incremental/step2")))->runMissingMigrations();

        $migrations = (new LoadMigrations($pdo))->allInstalledMigrations();
        $this->assertEquals("v3_test.sql", $migrations[0]->getFilename());
        $this->assertEquals("v10_test.sql", $migrations[1]->getFilename());
        $this->assertEquals("v13_test.sql", $migrations[2]->getFilename());
    }


}
