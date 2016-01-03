<?php
require __DIR__ . "/../vendor/autoload.php";

const DB_USER = "root";
const DB_PASSWORD = "password";
const DB_SCHEMA = "testdb";

define("DB_HOST", fromEnv("DATABASE_HOST"));

function fromEnv($key) {
    $value = getenv($key);
    if(!$value) {
        throw new Exception("Can't read environment variable $key. It's mandatory. (Are you using make tests?)");
    }
    return $value;
}