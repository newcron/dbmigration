<?php


namespace dbmigrate;


use dbmigrate\application\MigrationException;
use dbmigrate\application\schema\Migration;
use dbmigrate\application\sql\LoadMigrations;
use dbmigrate\application\sql\SqlFile;
use dbmigrate\testutil\IntegrationDatabase;

class MigrateDryRunIntegrationTest extends \PHPUnit_Framework_TestCase
{
    use IntegrationDatabase;


    public function testInitializeFromEmptySchema()
    {
        $pdo = $this->aNewInitializedSchema();

        $sqlDirectory = new \SplFileInfo(__DIR__ . "/../testsets/nummericsorting");

        $files = call_user_func(new MigrateDryRun($pdo, $sqlDirectory));

        $this->assertEquals([
            new SqlFile(new \SplFileInfo($sqlDirectory->getPathname()."/v3_test.sql")),
            new SqlFile(new \SplFileInfo($sqlDirectory->getPathname()."/v10_test.sql"))
        ], $files);
    }


}
