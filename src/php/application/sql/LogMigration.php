<?php


namespace dbmigrate\application\sql;


use dbmigrate\application\MigrationException;

class LogMigration
{
    /** @var \PDO */
    private $pdo;

    /**
     * LogMigration constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;

    }

    public function logSuccess(SqlFile $file)
    {
        $this->log($file, true);
    }

    public function logFailure(SqlFile $file) {
        $this->log($file, false);
    }

    /**
     * @param SqlFile $file
     * @throws MigrationException
     */
    private function log(SqlFile $file, $success)
    {
        $statement = $this->pdo->prepare("insert into installed_migrations(installation_time, migration_file_name, migration_file_checksum, success) values(now(), ?, ?, ?)");
        try {

            $success = $statement->execute([
                $file->getFile()->getFilename(),
                $file->getHash(),
                $success === true ? "true" : "false"
            ]);
            if ($success === false) {
                throw new MigrationException("Could not write to installed_migrations table: " . var_export($statement->errorInfo(), true));
            }
        } finally {
            $statement->closeCursor();
        }
    }

}