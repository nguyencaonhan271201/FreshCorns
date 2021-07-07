<?php
class Database {
    private static $instances = [];
    public $conn;

    public function __construct() {

    }

    public static function getInstance() : Database
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public function initializeDatabaseConnection() {
        $DB_HOST = "localhost";
        $DB_USER = "root";
        $DB_PASSWORD = "";
        $DB_NAME = "cs204_final_project";
        $DB_PORT = 3306;

        $this->conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
        mysqli_set_charset($this->conn, 'utf8mb4');
    }
}