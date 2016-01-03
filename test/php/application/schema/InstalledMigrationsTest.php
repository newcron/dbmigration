<?php


namespace dbmigrate\application\schema;


use dbmigrate\application\MigrationException;
use dbmigrate\testutil\MigrationFileMockFactory;

class InstalledMigrationsTest extends \PHPUnit_Framework_TestCase
{

    /** @var InstalledMigrations  */
    private $installedMigrations;



    public function setUp()
    {
        $this->installedMigrations = new InstalledMigrations([
           new Migration(1, new \DateTime(), "v1.sql", "checksum1", true),
           new Migration(2, new \DateTime(), "v2.sql", "checksum2", true),
        ]);
    }

    public function testIdentifiesInstalledMigration()
    {
        $this->assertFalse($this->installedMigrations->needsToBeInstalled(MigrationFileMockFactory::mockMigrationFile("v1.sql", "checksum1")));
    }

    public function testIdentifiesNotInstalledMigration()
    {
        $this->assertTrue($this->installedMigrations->needsToBeInstalled(MigrationFileMockFactory::mockMigrationFile("v3.sql", "checksum1")));
    }

    /** @expectedException \dbmigrate\application\MigrationException */
    public function testIdentifiesBrokenChecksum()
    {
        $this->installedMigrations->needsToBeInstalled(MigrationFileMockFactory::mockMigrationFile("v1.sql", "otherchecksum"));
    }


}
