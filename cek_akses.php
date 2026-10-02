<?php
// cek_akses.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika tidak ada session user_id, lempar kembali ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>