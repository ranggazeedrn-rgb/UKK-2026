<?php
session_start();

// Jika pengguna sudah login, arahkan langsung ke dashboard sesuai role masing-masing
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if ($_SESSION['role'] === 'Admin') {
        header("Location: dashboard_admin.php");
    } elseif ($_SESSION['role'] === 'Guru') {
        header("Location: dashboard_guru.php");
    } elseif ($_SESSION['role'] === 'Wali Kelas') {
        header("Location: dashboard_walikelas.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pelanggaran Siswa - UKK 2026</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            background-image: radial-gradient(#333 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: rgba(20, 20, 20, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .navbar .brand {
            font-size: 1.2rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }
        .navbar .btn-login-nav {
            padding: 8px 18px;
            background: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .navbar .btn-login-nav:hover {
            background: #0056b3;
        }
        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .card-landing {
            background: rgba(30, 30, 30, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 50px 40px;
            max-width: 650px;
            width: 100%;
            text-align: center;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
        }
        .card-landing h1 {
            font-size: 2.2rem;
            color: #ffffff;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .card-landing p {
            color: #aaaaaa;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-main {
            display: inline-block;
            padding: 14px 32px;
            background: #007bff;
            color: #ffffff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
        }
        .btn-main:hover {
            background: #0056b3;
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.6);
            transform: translateY(-2px);
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666666;
            font-size: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(15, 15, 15, 0.8);
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="brand">SISTEM PELANGGARAN SISWA</div>
        <a href="login.php" class="btn-login-nav">Masuk</a>
    </nav>

    <section class="hero-section">
        <div class="card-landing">
            <h1>Sistem Informasi Pelanggaran Siswa</h1>
            <p>Platform pencatatan, pemantauan, dan rekapitulasi kedisiplinan siswa secara real-time. Memudahkan Guru, Wali Kelas, dan Administrator dalam mengelola tata tertib sekolah secara transparan dan terintegrasi.</p>
            <a href="login.php" class="btn-main">Masuk ke Aplikasi</a>
        </div>
    </section>

    <footer class="footer">
        &copy; 2026 Sistem Pelanggaran Siswa - UKK RPL 2026. All rights reserved.
    </footer>

</body>
</html>