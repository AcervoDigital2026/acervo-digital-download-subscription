<?php

namespace App\Config;

class Database {
    private $host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'acervodigital_db';
        $this->db_user = getenv('DB_USER') ?: 'root';
        $this->db_pass = getenv('DB_PASS') ?: '';
    }

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new \mysqli(
                $this->host,
                $this->db_user,
                $this->db_pass,
                $this->db_name
            );

            if ($this->conn->connect_error) {
                throw new \Exception('Erro de conexão: ' . $this->conn->connect_error);
            }

            $this->conn->set_charset('utf8mb4');
            return $this->conn;
        } catch (\Exception $e) {
            die('ERRO DE BANCO DE DADOS: ' . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
