<?php


namespace dbmigrate\testutil;


use dbmigrate\Initialize;

trait IntegrationDatabase
{

    public function aNewSchema() {
        $host = sprintf("mysql:host=%s;database=%s;charset=utf8", DB_HOST, DB_SCHEMA);
        try {
            $pdo = new \PDO($host, DB_USER, DB_PASSWORD);
            $pdo->exec(sprintf("drop database %s; create database %s; use %s;", DB_SCHEMA, DB_SCHEMA, DB_SCHEMA));
            return $pdo;
        } catch(\PDOException $e) {
            throw new \Exception(sprintf("Can't connect to %s using %s and %s", $host, DB_USER, DB_PASSWORD), 1, $e);
        }
    }

    public function aNewInitializedSchema() {
        $pdo = $this->aNewSchema();

        (new Initialize($pdo))->createInstalledMigrationsTable();

        return $pdo;
    }

}