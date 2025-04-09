<?php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'esp32';
    private $connection;

    public function __construct() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>