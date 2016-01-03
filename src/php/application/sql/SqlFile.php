<?php


namespace dbmigrate\application\sql;


use dbmigrate\application\MigrationException;

class SqlFile
{
    /** @var \SplFileInfo */
    private $file;
    /** @var string */
    private $contents;

    public function __construct(\SplFileInfo $file)
    {
        if (!$file->isFile()) {
            throw new \InvalidArgumentException("File " . $file->getPathname() . " ist not a normal file");
        }

        if (!is_readable($file->getPathname())) {
            throw new MigrationException("File " . $file->getPathname() . " can't be read (no permissions)");
        }
        $this->file = $file;
        $this->contents = file_get_contents($file->getPathname());
        if ($this->contents === false) {
            $error = error_get_last();
            throw new MigrationException("File " . $file->getPathname() . " can't be read (" . $error["message"] . ")");
        }
    }

    /** @return \SplFileInfo */
    public function getFile()
    {
        return $this->file;
    }

    /** @return string */
    public function getContents()
    {
        return $this->contents;
    }

    public function getHash() {
        return md5($this->contents);
    }

}