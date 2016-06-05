<?php


namespace dbmigrate\application;


class MigrationDirectoryValidator
{
    public function assertValidMigrationFileDirectory(\SplFileInfo $sqlDirectory)
    {
        if(!$sqlDirectory->isDir() || !is_readable($sqlDirectory->getPathname())) {
            throw new \InvalidArgumentException("sqlDirectory ".$sqlDirectory->getPathname()." does not exist or is not readable");
        }

    }
}