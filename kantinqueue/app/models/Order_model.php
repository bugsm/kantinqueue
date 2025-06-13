<?php
class Order_model {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }

    public function getLatestQueueNumber() {
        $this->db->query("SELECT nomor_antrian FROM pesanan ORDER BY id_pesanan DESC LIMIT 1");
        $row = $this->db->single();
        if ($row) {
            return $row['nomor_antrian'];
        }
        return 'A-000';
    }

    public function processCheckout($id_mahasiswa, $cart) {
        $this->db->query("
            UPDATE pesanan
            SET status = 'kadaluarsa'
            WHERE id_mahasiswa = :id_mahasiswa
              AND status = 'dipesan'
              AND waktu_pesan < DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");
        $this->db->bind(':id_mahasiswa', $id_mahasiswa);
        $this->db->execute();
        $this->db->beginTransaction();

        try {
            $last_queue = $this->getLatestQueueNumber();
            $num = (int) substr($last_queue, 2);
            $new_num = $num + 1;
            $nomor_antrian = 'A-' . str_pad($new_num, 3, '0', STR_PAD_LEFT);

            $total_harga = 0;
            foreach ($cart as $item) {
                $total_harga += $item['harga'] * $item['jumlah'];
            }

            $this->db->query("INSERT INTO pesanan (id_mahasiswa, nomor_antrian, total_harga) VALUES (:id, :no_antrian, :total)");
            $this->db->bind(':id', $id_mahasiswa);
            $this->db->bind(':no_antrian', $nomor_antrian);
            $this->db->bind(':total', $total_harga);
            $this->db->execute();
            $id_pesanan_baru = $this->db->lastInsertId();

            
            foreach ($cart as $id_menu => $item) {
                $this->db->query("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, subtotal) VALUES (:id_pesanan, :id_menu, :jumlah, :subtotal)");
                $this->db->bind(':id_pesanan', $id_pesanan_baru);
                $this->db->bind(':id_menu', $id_menu);
                $this->db->bind(':jumlah', $item['jumlah']);
                $this->db->bind(':subtotal', $item['harga'] * $item['jumlah']);
                $this->db->execute();

                $this->db->query("UPDATE menu SET stok = stok - :jumlah WHERE id_menu = :id_menu");
                $this->db->bind(':jumlah', $item['jumlah']);
                $this->db->bind(':id_menu', $id_menu);
                $this->db->execute();
            }

            $this->db->query("CALL SetEstimasiSelesai(:id_pesanan)");
            $this->db->bind(':id_pesanan', $id_pesanan_baru);
            $this->db->execute();

            $this->db->commit();
            return ['status' => true, 'nomor_antrian' => $nomor_antrian];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getOrderDetails($nomor_antrian) {
        $this->db->query("SELECT * FROM pesanan WHERE nomor_antrian = :no_antrian");
        $this->db->bind(':no_antrian', $nomor_antrian);
        return $this->db->single();
    }
}