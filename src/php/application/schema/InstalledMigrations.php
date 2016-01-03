<?php


namespace dbmigrate\application\schema;


use dbmigrate\application\MigrationException;
use dbmigrate\application\sql\SqlFile;

class InstalledMigrations
{
    /** @var Migration[] */
    private $migrations;

    /**
     * InstalledMigrations constructor.
     * @param Migration[] $migrations
     */
    public function __construct(array $migrations)
    {
        $this->migrations = $migrations;
    }

    public function needsToBeInstalled(SqlFile $file)
    {
        return $this->counterpartOf($file) === null;
    }

    /** @return Migration */
    private function counterpartOf(SqlFile $file)
    {
        foreach ($this->migrations as $migration) {
            if ($file->getFile()->getFilename() == $migration->getFilename()) {
                $this->validateChecksum($file, $migration);

                return $migration;
            }
        }

        return null;
    }

    private function validateChecksum(SqlFile $file, Migration $migration)
    {
        if ($file->getHash() !== $migration->getChecksum()) {
            throw new MigrationException("Migration " . $file->getFile()->getFilename() . " is already installed, but it's contents were modified afterwards. Checksum at installation time: " . $migration->getChecksum() . "; current checksum: " . $file->getHash());
        }
    }

}