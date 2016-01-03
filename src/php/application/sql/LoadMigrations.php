<?php


namespace dbmigrate\application\sql;


use dbmigrate\application\schema\Migration;

class LoadMigrations
{
    /** @var  \PDO */
    private $pdo;

    /**
     * LoadMigrations constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return Migration[] */
    public function allInstalledMigrations()
    {
        (new MigrationTablePresenceConstraint($this->pdo))->assertTablePresent();

        $result = $this->pdo->query("select * from installed_migrations")->fetchAll();

        return array_map(function($object){
            return new Migration($object["id"], new \DateTime($object["installation_time"]), $object["migration_file_name"], $object["migration_file_checksum"], (bool)$object["success"]);
        }, $result);
    }


}