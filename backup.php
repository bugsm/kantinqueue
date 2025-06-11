<?php
date_default_timezone_set('Asia/Jakarta');
$mysqldump_path = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe';

require_once __DIR__ . '/app/init.php';
$backup_dir = __DIR__ . '/backupdatabase';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

$date = date('Y-m-d_H-i-s');
$backup_file_path = $backup_dir . "/kantinqueue_backup_$date.sql";

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

if ($return_var === 0 && file_exists($backup_file_path)) {
    $log_message = date('Y-m-d H:i:s') . " - SUKSES: Backup berhasil disimpan ke: " . $backup_file_path;
    echo $log_message;
} else {
    $log_message = date('Y-m-d H:i:s') . " - GAGAL: Proses backup database gagal. Kode Error: $return_var. Perintah: $command";
    echo $log_message;
}
?>