<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$user_name = $_SESSION['name'];

$dash_url = 'dashboard_admin.php';
if ($role === 'Guru') $dash_url = 'dashboard_guru.php';
elseif ($role === 'Wali Kelas') $dash_url = 'dashboard_walikelas.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelanggaran Siswa - UKK 2026</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #e0e0e0; background-image: radial-gradient(#333 1px, transparent 1px); background-size: 20px 20px; min-height: 100vh; padding-bottom: 40px; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: rgba(20, 20, 20, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 30px; }
        .navbar .brand { font-size: 1.1rem; font-weight: 700; color: #3b82f6; text-decoration: none; }
        .navbar .user-info { font-size: 0.9rem; color: #aaa; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 20px; }
        .card { background: rgba(30, 30, 30, 0.85); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 25px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); backdrop-filter: blur(10px); margin-bottom: 25px; }
        h1, h2, h3 { color: #ffffff; font-weight: 600; margin-bottom: 15px; }
        .btn { display: inline-block; padding: 8px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: background 0.2s; }
        .btn:hover { background: #1d4ed8; }
        .btn-success { background: #059669; } .btn-success:hover { background: #047857; }
        .btn-danger { background: #dc2626; } .btn-danger:hover { background: #b91c1c; }
        .btn-warning { background: #d97706; color: #fff; } .btn-warning:hover { background: #b45309; }
        .btn-secondary { background: #4b5563; } .btn-secondary:hover { background: #374151; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 0.88rem; color: #ccc; }
        .form-control { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background: #222; color: #fff; font-size: 0.95rem; }
        .form-control:focus { outline: none; border-color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.9rem; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #333; text-align: left; }
        th { background: rgba(255, 255, 255, 0.05); color: #fff; }
        tr:hover { background: rgba(255, 255, 255, 0.02); }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-danger { background: rgba(220, 38, 38, 0.2); color: #f87171; border: 1px solid rgba(220, 38, 38, 0.4); }
        .badge-success { background: rgba(5, 150, 105, 0.2); color: #34d399; border: 1px solid rgba(5, 150, 105, 0.4); }
        .badge-warning { background: rgba(217, 119, 6, 0.2); color: #fbbf24; border: 1px solid rgba(217, 119, 6, 0.4); }
        .alert { padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: rgba(5, 150, 105, 0.15); border: 1px solid rgba(5, 150, 105, 0.3); color: #34d399; }
        .alert-danger { background: rgba(220, 38, 38, 0.15); border: 1px solid rgba(220, 38, 38, 0.3); color: #f87171; }
        .actions { display: flex; gap: 6px; }
        .grid-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="<?= $dash_url ?>" class="brand">SISTEM PELANGGARAN SISWA</a>
        <div class="user-info">
            Login sebagai: <strong><?= htmlspecialchars($user_name) ?></strong> (<?= htmlspecialchars($role) ?>) | 
            <a href="logout.php" style="color: #f87171; text-decoration: none; font-weight: 600; margin-left: 8px;">Logout</a>
        </div>
    </nav>
    <div class="container">

<?php
$total_pelanggaran = @$pdo->query("SELECT COUNT(*) FROM t_pelanggaran_siswa")->fetchColumn() ?: 0;
?>

<div class="card">
    <h1>Dashboard Guru</h1>
    <p style="color:#aaa;">Selamat datang, Bapak/Ibu <strong><?= htmlspecialchars($user_name) ?></strong>. Silakan gunakan menu di bawah untuk mencatat dan memantau kedisiplinan siswa.</p>
    
    <div style="margin:25px 0;">
        <div style="background:rgba(255,255,255,0.05); padding:20px; border-radius:8px; text-align:center; border:1px solid rgba(255,255,255,0.1); max-width:300px;">
            <h2 style="color:#f87171; font-size:2rem;"><?= $total_pelanggaran ?></h2>
            <p style="color:#aaa;">Total Pelanggaran Dicatat</p>
        </div>
    </div>

    <h3>Menu Guru</h3>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="app_catat_pelanggaran.php" class="btn btn-danger">+ Catat Pelanggaran Siswa</a>
        <a href="app_tindakan.php" class="btn btn-warning">Tindakan Pendisiplinan</a>
        <a href="app_laporan.php" class="btn btn-success">Laporan Pelanggaran</a>
        <a href="app_riwayat.php" class="btn">Riwayat</a>
    </div>
</div>

    </div>
</body>
</html>
