<?php
require_once __DIR__ . '/../config/database.php';

abstract class BaseModel {
    protected ?PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getDb(): PDO {
        if (!$this->db) {
            $this->db = Database::getConnection();
        }
        return $this->db;
    }
}
