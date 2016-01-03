<?php


namespace dbmigrate\application;


class MigrationException extends \Exception
{

    /**
     * MigrationException constructor.
     */
    public function __construct($message, $exception = null)
    {
        parent::__construct($message, 1, $exception);
    }
}