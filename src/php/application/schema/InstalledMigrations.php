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
        return $this->isInstalledAndSuccessful($file) === null;
    }

    /**
     * @param SqlFile $file
     *
     * @return Migration
     * @throws MigrationException
     */
    private function isInstalledAndSuccessful(SqlFile $file)
    {
        foreach ($this->migrations as $migration) {
            if ($file->getFile()->getFilename() == $migration->getFilename()) {
                $this->validateChecksum($file, $migration);

                // Exclude successfully installed migrations
                if($migration->getSuccess()){
                    return null;
                }

                return $migration;
            }
        }

        return null;
    }

    private function validateChecksum(SqlFile $file, Migration $migration)
    {
        if ($file->getHash() !== $migration->getChecksum()) {
            throw new MigrationException("Migration " . $file->getFile()->getFilename() . " was already installed (possibly failed), but it's contents were modified afterwards. Checksum at installation time: " . $migration->getChecksum() . "; current checksum: " . $file->getHash());
        }
    }

}
