<?php


namespace dbmigrate\application;


class MigrationDirectoryValidator
{
    public function assertValidMigrationFileDirectory(\SplFileInfo $sqlDirectory)
    {
        if($sqlDirectory===null) {
            throw new \InvalidArgumentException("sqlDirectory may not be null");
        }

        if(!$sqlDirectory->isDir() || !is_readable($sqlDirectory->getPathname())) {
            throw new \InvalidArgumentException("sqlDirectory ".$sqlDirectory->getPathname()." does not exist or is not readable");
        }

    }
}