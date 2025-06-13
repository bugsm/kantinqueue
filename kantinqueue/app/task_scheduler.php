<?php
require_once __DIR__ . '/init.php';

class TaskScheduler {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Backup database setiap hari pada jam 00:00
    public function scheduleBackup() {
        $backup_dir = __DIR__ . '/../backupdatabase';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0777, true);
        }

        $date = date('Y-m-d_H-i-s');
        $backup_file_path = $backup_dir . "/kantinqueue_backup_$date.sql";
        
        $mysqldump_path = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe';
        $password_arg = defined('DB_PASS') && DB_PASS ? '-p"' . DB_PASS . '"' : '';
        
        $command = sprintf(
            '"%s" --user=%s %s --host=%s %s > "%s"',
            $mysqldump_path,
            DB_USER,
            $password_arg,
            DB_HOST,
            DB_NAME,
            $backup_file_path
        );

        exec($command, $output, $return_var);
        
        // Hapus backup yang lebih dari 7 hari
        $this->cleanOldBackups($backup_dir);
    }

    // Bersihkan backup yang lebih dari 7 hari
    private function cleanOldBackups($backup_dir) {
        $files = glob($backup_dir . "/*.sql");
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 7 * 24 * 60 * 60) { // 7 hari
                    unlink($file);
                }
            }
        }
    }

    // Update status pesanan yang kadaluarsa
    public function updateExpiredOrders() {
        $query = "UPDATE pesanan 
                 SET status = 'kadaluarsa' 
                 WHERE status IN ('dipesan', 'diproses') 
                 AND estimasi_selesai < NOW()";
        $this->db->query($query);
    }

    // Generate laporan harian
    public function generateDailyReport() {
        $query = "INSERT INTO statistik_harian (tanggal, total_pesanan_masuk, total_pendapatan)
                 SELECT 
                    CURDATE(),
                    COUNT(*) as total_pesanan,
                    SUM(total_harga) as total_pendapatan
                 FROM pesanan 
                 WHERE DATE(waktu_pesan) = CURDATE()";
        $this->db->query($query);
    }
}

// Jalankan scheduler
$scheduler = new TaskScheduler();

// Backup database
$scheduler->scheduleBackup();

// Update pesanan kadaluarsa
$scheduler->updateExpiredOrders();

// Generate laporan harian
$scheduler->generateDailyReport(); 