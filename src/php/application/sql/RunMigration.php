<?php


namespace dbmigrate\application\sql;


use dbmigrate\application\MigrationException;
use dbmigrate\application\sql\SqlFile;

class RunMigration
{
    /** @var  \PDO */
    private $pdo;

    /**
     * ScriptRunner constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        if ($pdo === null) {
            throw new \InvalidArgumentException("PDO may not be null");
        }
        $this->pdo = $pdo;
    }


    public function run(SqlFile $file)
    {
        $statement = $this->pdo->prepare($file->getContents());
        try {
            $result = $statement->execute();
            if ($result === false) {
                throw new \Exception("Query Returned " . var_export($this->pdo->errorInfo(), true));
            }
        } catch (\Exception $e) {
            throw new MigrationException("Running SQL File " . $file->getFile()->getPathname() . " failed.", $e);
        } finally {
            $statement->closeCursor();
        }
    }

}