<?php

namespace App\Models;

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $db_config = new \App\Config\Database();
        $this->db = $db_config->connect();
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            'INSERT INTO ' . $this->table . ' 
            (name, email, password, phone, document, role, subscription_status, is_verified, verification_token, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );

        $stmt->bind_param(
            'sssssssss',
            $data['name'],
            $data['email'],
            $data['password'],
            $data['phone'],
            $data['document'],
            $data['role'],
            $data['subscription_status'],
            $data['is_verified'],
            $data['verification_token']
        );

        return $stmt->execute();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findById($id) {
        $stmt = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function verify($email) {
        $stmt = $this->db->prepare('UPDATE ' . $this->table . ' SET is_verified = 1, verification_token = NULL WHERE email = ?');
        $stmt->bind_param('s', $email);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $fields = [];
        $types = '';
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = $key . ' = ?';
            $types .= 's';
            $values[] = $value;
        }

        $values[] = $id;
        $types .= 'i';

        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
    }
}
