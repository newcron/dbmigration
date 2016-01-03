<?php


namespace dbmigrate\application\sql;


use dbmigrate\testutil\IntegrationDatabase;
use dbmigrate\testutil\MigrationFileMockFactory;

class LogMigrationIntegrationTest extends \PHPUnit_Framework_TestCase
{

    private $pdo;
    private $mock;
    use IntegrationDatabase;

    public function setUp()
    {
        $this->pdo = $this->aNewInitializedSchema();

        $this->mock = MigrationFileMockFactory::mockMigrationFile("v0_test.sql", "hash");

    }


    public function testLogsSuccessCorrectly()
    {

        (new LogMigration($this->pdo))->logSuccess($this->mock);

        $entry = $this->pdo->query("select * from installed_migrations;")->fetchObject();

        $this->assertEquals("v0_test.sql", $entry->migration_file_name);
        $this->assertEquals("hash", $entry->migration_file_checksum);
        $this->assertEquals("true", $entry->success);
    }

    public function testLogsFailureCorrectly()
    {

        (new LogMigration($this->pdo))->logFailure($this->mock);

        $entry = $this->pdo->query("select * from installed_migrations;")->fetchObject();

        $this->assertEquals("v0_test.sql", $entry->migration_file_name);
        $this->assertEquals("hash", $entry->migration_file_checksum);
        $this->assertEquals("false", $entry->success);
    }
}
