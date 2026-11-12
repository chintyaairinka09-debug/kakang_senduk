<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Jakarta');
define('BASE_URL', '/voting-kakang-senduk');
define('APP_NAME', 'Kakang Senduk Ponorogo');
define('VOTING_FEE', 3000);
require_once __DIR__ . '/koneksi.php';

function url(string $path = ''): string {
    return BASE_URL . ($path !== '' ? '/' . ltrim($path, '/') : '');
}
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}
function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) { $_SESSION['_flash'][$key] = $message; return null; }
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}
function csrf_field(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    return '<input type="hidden" name="_csrf" value="' . e($_SESSION['_csrf']) . '">';
}
function verify_csrf(): void {
    $token = $_POST['_csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Sesi formulir kedaluwarsa. Silakan kembali dan muat ulang halaman.');
    }
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void {
    if (!current_user()) { flash('error', 'Silakan login terlebih dahulu.'); redirect('auth/login.php'); }
}
function require_admin(): void {
    require_login();
    if ((current_user()['role'] ?? '') !== 'admin') { flash('error', 'Halaman ini khusus administrator.'); redirect('pages/index.php'); }
}
function rupiah($amount): string { return 'Rp' . number_format((float)$amount, 0, ',', '.'); }
function active_period(PDO $pdo): ?array {
    $stmt = $pdo->query("SELECT * FROM periode_voting WHERE status = 'Aktif' AND NOW() BETWEEN tanggal_mulai AND tanggal_selesai ORDER BY id DESC LIMIT 1");
    return $stmt->fetch() ?: null;
}
function current_period(PDO $pdo): ?array {
    $stmt = $pdo->query("SELECT * FROM periode_voting ORDER BY id DESC LIMIT 1");
    return $stmt->fetch() ?: null;
}
function paid_for_period(PDO $pdo, int $userId, int $periodId): bool {
    $stmt = $pdo->prepare("SELECT id FROM payments WHERE user_id = ? AND periode_id = ? AND status = 'paid' LIMIT 1");
    $stmt->execute([$userId, $periodId]);
    return (bool)$stmt->fetchColumn();
}
function voted_category(PDO $pdo, int $userId, int $periodId, string $category): bool {
    $stmt = $pdo->prepare('SELECT id FROM voting WHERE user_id = ? AND periode_id = ? AND kategori = ? LIMIT 1');
    $stmt->execute([$userId, $periodId, $category]);
    return (bool)$stmt->fetchColumn();
}
function upload_photo(string $field, ?string $old = null): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return $old;
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload foto gagal.');
    if ($file['size'] > 3 * 1024 * 1024) throw new RuntimeException('Ukuran foto maksimal 3 MB.');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Foto harus berformat JPG, PNG, atau WEBP.');
    $name = 'kontestan_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $destination = dirname(__DIR__) . '/uploads/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $destination)) throw new RuntimeException('Foto tidak dapat disimpan.');
    return $name;
}
