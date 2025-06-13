<?php
class Menu_model {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }

    public function getAllMenu() {
        $this->db->query("SELECT * FROM menu WHERE stok > 0 ORDER BY nama_menu ASC");
        return $this->db->resultSet();
    }
}