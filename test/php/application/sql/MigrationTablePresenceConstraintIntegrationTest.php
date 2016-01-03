<?php


namespace dbmigrate\application\sql;


use dbmigrate\application\MigrationException;
use dbmigrate\Initialize;
use dbmigrate\testutil\IntegrationDatabase;

class MigrationTablePresenceConstraintIntegrationTest extends \PHPUnit_Framework_TestCase
{
    use IntegrationDatabase;


    /**
     * @expectedException \dbmigrate\application\MigrationException
     */
    public function testValidationFailsOnMissingTable()
    {
        (new MigrationTablePresenceConstraint($this->aNewSchema()))->assertTablePresent();
    }

    public function testValidationPassesOnExistingTable()
    {
        $pdo = $this->aNewSchema();

        (new Initialize($pdo))->createInstalledMigrationsTable();

        (new MigrationTablePresenceConstraint($pdo))->assertTablePresent();
    }


}
