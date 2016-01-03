<?php


use dbmigrate\Initialize;
use dbmigrate\application\sql\RunMigration;
use dbmigrate\testutil\IntegrationDatabase;

class InitializeIntegrationTest extends PHPUnit_Framework_TestCase
{
    use IntegrationDatabase;

    public function testInitializeCreatesExactlyOneTable()
    {
        $pdo = $this->aNewSchema();
        (new Initialize($pdo))->createInstalledMigrationsTable();

        $this->assertEquals(1, $pdo->query("show tables;")->rowCount());
    }

    public function testInitializeCreatesInfoTable()
    {
        $pdo = $this->aNewSchema();
        (new Initialize($pdo))->createInstalledMigrationsTable();

        $this->assertEquals("installed_migrations", $pdo->query("show tables;")->fetchColumn(0));
    }
}