<?php


namespace dbmigrate\application;


use dbmigrate\application\sql\SqlFile;

class MigrationFileScanner
{
    /** @var  \SplFileInfo */
    private $scanDir;

    /**
     * MigrationFileScanner constructor.
     * @param \SplFileInfo $scanDir
     */
    public function __construct(\SplFileInfo $scanDir)
    {
        (new MigrationDirectoryValidator())->assertValidMigrationFileDirectory($scanDir);
        $this->scanDir = $scanDir;
    }

    /** @return SqlFile[] */
    public function listSqlFiles()
    {
        $foundFiles = glob($this->scanDir->getPathname() . "/*.[sS][qQ][lL]");
        natsort($foundFiles);
        $foundFiles = array_values($foundFiles);

        return array_map(function ($file) {
            return new SqlFile(new \SplFileInfo($file));
        }, $foundFiles);
    }

}