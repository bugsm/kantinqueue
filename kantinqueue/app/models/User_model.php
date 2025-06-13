<?php
class User_model {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function findUserByNim($nim) {
        $this->db->query('SELECT * FROM mahasiswa WHERE nim = :nim');
        $this->db->bind(':nim', $nim);
        return $this->db->single();
    }

    public function register($data) {
        $this->db->query('INSERT INTO mahasiswa (nim, nama, password) VALUES (:nim, :nama, :password)');
        $this->db->bind(':nim', $data['nim']);
        $this->db->bind(':nama', $data['nama']);
        $this->db->bind(':password', $data['password']);
        $this->db->execute();
        return $this->db->rowCount();
    }
}