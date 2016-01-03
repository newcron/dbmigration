<?php


namespace dbmigrate\testutil;


use dbmigrate\application\sql\SqlFile;

class MigrationFileMockFactory
{

    public static function mockMigrationFile($filename, $hash, $contents="select 1;")
    {
        $sqlFile = \Phake::mock(SqlFile::class);
        \Phake::when($sqlFile)->getFile()->thenReturn(new \SplFileInfo("/foo/$filename"));
        \Phake::when($sqlFile)->getHash()->thenReturn($hash);
        \Phake::when($sqlFile)->getContents()->thenReturn($contents);

        return $sqlFile;
    }

}