<?php


namespace dbmigrate\application\sql;


use dbmigrate\testutil\IntegrationDatabase;
use dbmigrate\testutil\MigrationFileMockFactory;

class LoadMigrationsTest extends \PHPUnit_Framework_TestCase
{

    use IntegrationDatabase;


    public function testReturnsEmptyArrayWhenNoMigrationsPresent()
    {
        $pdo = $this->aNewInitializedSchema();

        $this->assertEquals([], (new LoadMigrations($pdo))->allInstalledMigrations());
    }

    public function testReturnsListWithTwoInstalledMigrations()
    {
        $pdo = $this->aNewInitializedSchema();
        $logMigration = new LogMigration($pdo);

        $logMigration->logSuccess(MigrationFileMockFactory::mockMigrationFile("v0_test.sql", "hash0"));
        $logMigration->logSuccess(MigrationFileMockFactory::mockMigrationFile("v1_test.sql", "hash1"));

        $allInstalledMigrations = (new LoadMigrations($pdo))->allInstalledMigrations();

        $this->assertEquals(2, sizeof($allInstalledMigrations));
        $this->assertEquals("v0_test.sql", $allInstalledMigrations[0]->getFilename());
        $this->assertEquals("v1_test.sql", $allInstalledMigrations[1]->getFilename());
    }


    public function testReturnsCompleteMigrationObject()
    {
        $pdo = $this->aNewInitializedSchema();
        $logMigration = new LogMigration($pdo);

        $logMigration->logSuccess(MigrationFileMockFactory::mockMigrationFile("v0_test.sql", "hash0"));
        $migration = (new LoadMigrations($pdo))->allInstalledMigrations()[0];

        $this->assertEquals("1", $migration->getId());
        $this->assertTrue($migration->getInstalledAt() instanceof \DateTime);
        $this->assertEquals("v0_test.sql", $migration->getFilename());
        $this->assertEquals("hash0", $migration->getChecksum());
        $this->assertEquals(true, $migration->getSuccess());

    }


}
