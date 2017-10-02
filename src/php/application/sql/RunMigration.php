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
        $this->pdo->beginTransaction();

        try {
            $result = $this->pdo->query($file->getContents());
            if ($result === false) {
                $this->pdo->rollBack();
                throw new \Exception("Query Returned " . var_export($this->pdo->errorInfo(), true));
            }
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw new MigrationException("Error running SQL File " . $file->getFile()->getPathname() . " failed: ".$e->getMessage(), $e);
        }

        $this->pdo->commit();
    }

}