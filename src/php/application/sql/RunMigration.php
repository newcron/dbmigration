<?php

namespace dbmigrate\application\sql;

use dbmigrate\application\MigrationException;

class RunMigration
{
    /** @var  \PDO */
    private $pdo;

    /**
     * @var QuerySplitter
     */
    private $querySplitter;

    /**
     * ScriptRunner constructor.
     *
     * @param \PDO          $pdo
     * @param QuerySplitter $querySplitter
     */
    public function __construct(\PDO $pdo, QuerySplitter $querySplitter)
    {
        if ($pdo === null) {
            throw new \InvalidArgumentException("PDO may not be null");
        }
        $this->pdo = $pdo;

        $this->querySplitter = $querySplitter;
    }


    public function run(SqlFile $file)
    {
        $this->pdo->beginTransaction();

        $queries = $this->querySplitter->split($file->getContents());

        foreach ($queries as $query) {

            try {
                $result = $this->pdo->query($query);
                if ($result === false) {
                    $this->pdo->rollBack();

                    throw new \Exception("Query Returned " . var_export($this->pdo->errorInfo(), true));
                }
            } catch (\Exception $e) {
                $this->pdo->rollBack();
                throw new MigrationException(
                    "Error running SQL File " . $file->getFile()->getPathname() . " failed: " . $e->getMessage(), $e
                );
            }
        }

        $this->pdo->commit();
    }

}
