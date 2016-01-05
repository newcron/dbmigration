<?php


namespace dbmigrate;


use dbmigrate\application\MigrationException;
use dbmigrate\application\schema\Migration;
use dbmigrate\application\sql\LoadMigrations;
use dbmigrate\testutil\IntegrationDatabase;

class MigrateTest extends \PHPUnit_Framework_TestCase
{
    use IntegrationDatabase;


    public function testInitializeFromEmptySchema()
    {
        $pdo = $this->aNewInitializedSchema();

        call_user_func(new Migrate($pdo, new \SplFileInfo(__DIR__ . "/../testsets/nummericsorting")));

        $migrations = (new LoadMigrations($pdo))->allInstalledMigrations();
        $this->assertEquals("v3_test.sql", $migrations[0]->getFilename());
        $this->assertEquals("v10_test.sql", $migrations[1]->getFilename());
    }

    public function testInitializeIncremental()
    {

        $pdo = $this->aNewInitializedSchema();

        call_user_func(new Migrate($pdo, new \SplFileInfo(__DIR__ . "/../testsets/incremental/step1")));
        call_user_func(new Migrate($pdo, new \SplFileInfo(__DIR__ . "/../testsets/incremental/step2")));

        $migrations = (new LoadMigrations($pdo))->allInstalledMigrations();
        $this->assertEquals("v3_test.sql", $migrations[0]->getFilename());
        $this->assertEquals("v10_test.sql", $migrations[1]->getFilename());
        $this->assertEquals("v13_test.sql", $migrations[2]->getFilename());
    }

    public function testMarkBrokenSqlFileAsFailed()
    {
        $pdo = $this->aNewInitializedSchema();

        try {
            call_user_func(new Migrate($pdo, new \SplFileInfo(__DIR__ . "/../testsets/failing")));
            $this->fail("should throw an exception");
        } catch(MigrationException $e) {
            // expected
        }

        $migrations = (new LoadMigrations($pdo))->allInstalledMigrations();
        $this->assertFalse($migrations[0]->getSuccess());
    }


}
