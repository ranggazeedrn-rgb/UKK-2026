<?php 
session_start(); 
require_once 'config/koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Guru') { 
    header("Location: login.php"); 
    exit; 
} 

// --- QUERY PENGAMBILAN DATA (DASHBOARD GURU) ---
$total_pelanggaran = $pdo->query("SELECT COUNT(*) FROM t_pelanggaran_siswa")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru</title>
    <style>
        /* Menggunakan CSS yang sama persis seperti admin di atas */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #e0e0e0; background-image: radial-gradient(#333 1px, transparent 1px); background-size: 20px 20px; margin: 0; padding: 30px; }
        .container { background: rgba(30, 30, 30, 0.8); padding: 25px 35px; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); max-width: 1000px; margin: auto; }
        h1, h2 { color: #ffffff; font-weight: 600; margin-top: 0; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255,255,255,0.1); padding: 20px; border-radius: 8px; text-align: center; }
        .stat-card h3 { margin: 0; font-size: 2em; color: #ff4d4d; }
        .stat-card p { margin: 5px 0 0 0; color: #aaa; }

        .btn-danger { display: inline-block; padding: 10px 18px; background: #dc3545; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-bottom: 20px; transition: background 0.3s;}
        .btn-danger:hover { background: #c82333; }
        .nav-links { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; gap: 10px; flex-wrap: wrap; }
        .nav-links a { color: #aaa; text-decoration: none; padding: 8px 12px; border-radius: 4px; background: rgba(255,255,255,0.05); }
        .nav-links a:hover { color: #fff; background: rgba(255,255,255,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Guru</h1>
        <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>.</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $total_pelanggaran; ?></h3>
                <p>Total Kasus Pelanggaran Tercatat</p>
            </div>
        </div>

        <div class="nav-links">
            <a href="app_catat_pelanggaran.php">Catat Pelanggaran</a>
            <a href="app_tindakan.php">Tindakan</a>
            <a href="app_laporan.php">Laporan</a>
            <a href="app_riwayat.php">Riwayat</a>
        </div>
        
        <a href="logout.php" class="btn-danger" style="margin-top: 20px;">Logout</a>
    </div>
</body>
</html>