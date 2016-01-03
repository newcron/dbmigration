<?php


namespace dbmigrate\application\sql;


use dbmigrate\application\MigrationException;

class MigrationTablePresenceConstraint
{
    /** @var  \PDO */
    private $pdo;

    /**
     * MigrationTablePresenceConstraint constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function assertTablePresent()
    {
        if ($this->countRows() !== 1) {
            throw new MigrationException("Can't run Migrations as the installed_migrations table is missing. Please run initialize first");
        }
    }

    public function assertTableMissing()
    {
        if ($this->countRows() !== 0) {
            throw new MigrationException("Can't run Initialization as the installed_migrations table is existing.");
        }
    }

    /**
     * @return int
     */
    private function countRows()
    {

        $statement = $this->pdo->query("show tables like \"installed_migrations\";");
        try {
            return $statement->rowCount();
        } finally {
            $statement->closeCursor();
        }
    }
}