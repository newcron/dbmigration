<?php


namespace dbmigrate\application;


use dbmigrate\application\sql\SqlFile;

class MigrationFileScannerTest extends \PHPUnit_Framework_TestCase
{

    const TESTSET_DIR = __DIR__ . "/../../testsets";

    /** @expectedException \InvalidArgumentException */
    public function testFailsOnMissingDirectory()
    {
        new MigrationFileScanner(new \SplFileInfo("/foobar"));
    }

    public function testReturnsEmptyArrayOnEmptyDir()
    {
        $this->assertEquals([],
            (new MigrationFileScanner(new \SplFileInfo(static::TESTSET_DIR . "/emptyset/")))->listSqlFiles());
    }

    public function testReturnsNaturallySortedArray()
    {
        $sortedSet = (new MigrationFileScanner(new \SplFileInfo(static::TESTSET_DIR . "/nummericsorting/")))->listSqlFiles();
        $this->assertEquals([
            new SqlFile(new \SplFileInfo(static::TESTSET_DIR . "/nummericsorting/v3_test.sql")),
            new SqlFile(new \SplFileInfo(static::TESTSET_DIR . "/nummericsorting/v10_test.sql")),

        ],
            $sortedSet);
    }


}
