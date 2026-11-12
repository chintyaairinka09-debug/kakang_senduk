<?php
// Konfigurasi koneksi database untuk XAMPP.
$host = '127.0.0.1';
$db   = 'db_voting_kakang_senduk';
$user = 'root';
$pass = ''; // Bawaan XAMPP biasanya kosong. Sesuaikan bila root memakai password.
$charset = 'utf8mb4';
$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL aktif dan database db_voting_kakang_senduk sudah di-import. Detail: ' . htmlspecialchars($e->getMessage()));
}
