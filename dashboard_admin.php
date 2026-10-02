<?php
// dashboard_admin.php
session_start();
include 'config/koneksi.php'; // Pastikan file koneksi.php berada di folder yang sama
include 'cek_akses.php';

// FIX: Menggunakan format mysqli_query procedural untuk mencegah error "Call to a member function query() on null"
$query_siswa = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_siswa");
$total_siswa = mysqli_fetch_assoc($query_siswa)['total'] ?? 0;

$query_guru = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_guru");
$total_guru = mysqli_fetch_assoc($query_guru)['total'] ?? 0;

$query_pelanggaran = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa");
$total_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)['total'] ?? 0;

// Ambil 5 data pelanggaran terbaru untuk tabel preview
$query_recent = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa ORDER BY tanggal DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Kedisiplinan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Modern UI: Radial dots background */
        .bg-radial-dots {
            background-color: #0f172a;
            background-image: radial-gradient(#334155 1px, transparent 1px);
            background-size: 24px 24px;
        }
        /* Glassmorphism utilities */
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen font-sans p-6 md:p-10">

    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-indigo-500"></i> Admin Panel
                </h1>
                <p class="text-slate-400 mt-1">Sistem Kedisiplinan Siswa - SMK Muhammadiyah Tasikmalaya</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <div class="text-sm font-medium text-white"><?php echo $_SESSION['name'] ?? 'Administrator'; ?></div>
                    <div class="text-xs text-indigo-400 font-mono capitalize">Role: <?php echo $_SESSION['role'] ?? 'admin'; ?></div>
                </div>
                <a href="logout.php" class="bg-rose-900/40 hover:bg-rose-800/80 border border-rose-700/50 text-rose-300 px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 text-sm font-medium">
                    <i class="fa-solid fa-power-off"></i> Keluar
                </a>
            </div>
        </div>

        <!-- Statistik Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Card 1 -->
            <div class="glass-card p-6 rounded-2xl flex items-center gap-5 shadow-lg">
                <div class="w-14 h-14 rounded-xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 text-indigo-400 text-2xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h3 class="text-slate-400 text-sm font-medium">Total Siswa Aktif</h3>
                    <p class="text-3xl font-bold text-white mt-1"><?php echo $total_siswa; ?></p>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="glass-card p-6 rounded-2xl flex items-center gap-5 shadow-lg">
                <div class="w-14 h-14 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-400 text-2xl">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div>
                    <h3 class="text-slate-400 text-sm font-medium">Total Guru</h3>
                    <p class="text-3xl font-bold text-white mt-1"><?php echo $total_guru; ?></p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="glass-card p-6 rounded-2xl flex items-center gap-5 shadow-lg">
                <div class="w-14 h-14 rounded-xl bg-amber-500/10 flex items-center justify-center border border-amber-500/20 text-amber-400 text-2xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-slate-400 text-sm font-medium">Total Kasus Tercatat</h3>
                    <p class="text-3xl font-bold text-white mt-1"><?php echo $total_pelanggaran; ?></p>
                </div>
            </div>
        </div>

        <!-- Layout 2 Kolom: Menu Navigasi & Tabel Preview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Menu Navigasi -->
            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold mb-5 flex items-center gap-2 text-white">
                    <i class="fa-solid fa-layer-group text-indigo-400"></i> Menu Master & Transaksi
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <a href="kelola_siswa.php" class="glass-card hover:bg-slate-800/80 p-5 rounded-xl text-center transition-all duration-300 group">
                        <i class="fa-solid fa-user-graduate block text-3xl mb-3 text-slate-500 group-hover:text-indigo-400 transition-colors"></i>
                        <span class="text-sm font-medium">Data Siswa</span>
                    </a>
                    <a href="kelola_guru.php" class="glass-card hover:bg-slate-800/80 p-5 rounded-xl text-center transition-all duration-300 group">
                        <i class="fa-solid fa-user-tie block text-3xl mb-3 text-slate-500 group-hover:text-emerald-400 transition-colors"></i>
                        <span class="text-sm font-medium">Data Guru</span>
                    </a>
                    <a href="kelola_kelas.php" class="glass-card hover:bg-slate-800/80 p-5 rounded-xl text-center transition-all duration-300 group">
                        <i class="fa-solid fa-school block text-3xl mb-3 text-slate-500 group-hover:text-blue-400 transition-colors"></i>
                        <span class="text-sm font-medium">Data Kelas</span>
                    </a>
                    <a href="kelola_jenis_pelanggaran.php" class="glass-card hover:bg-slate-800/80 p-5 rounded-xl text-center transition-all duration-300 group">
                        <i class="fa-solid fa-list-check block text-3xl mb-3 text-slate-500 group-hover:text-amber-400 transition-colors"></i>
                        <span class="text-sm font-medium">Jenis Pelanggaran</span>
                    </a>
                    <a href="catatan_pelanggaran.php" class="glass-card bg-indigo-900/20 hover:bg-indigo-900/40 border-indigo-500/30 p-5 rounded-xl text-center transition-all duration-300 group">
                        <i class="fa-solid fa-clipboard-list block text-3xl mb-3 text-indigo-400 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-bold text-indigo-200">Catat Pelanggaran</span>
                    </a>
                    <a href="laporan.php" class="glass-card hover:bg-slate-800/80 p-5 rounded-xl text-center transition-all duration-300 group">
                        <i class="fa-solid fa-print block text-3xl mb-3 text-slate-500 group-hover:text-slate-300 transition-colors"></i>
                        <span class="text-sm font-medium">Cetak Laporan</span>
                    </a>
                </div>
            </div>

            <!-- Kolom Kanan: Aktivitas Terkini -->
            <div class="glass-card p-6 rounded-2xl shadow-lg">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-lg font-bold text-white">Kasus Terkini</h2>
                    <a href="riwayat.php" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">Lihat Semua</a>
                </div>
                
                <div class="space-y-4">
                    <?php if (mysqli_num_rows($query_recent) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_recent)): ?>
                        <div class="p-3 bg-slate-800/50 rounded-lg border border-slate-700/50">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-medium text-sm text-white"><?php echo htmlspecialchars($row['nama_siswa']); ?></span>
                                <span class="text-[10px] text-slate-400 font-mono"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></span>
                            </div>
                            <div class="text-xs text-slate-400 truncate mb-2">Kelas: <?php echo htmlspecialchars($row['nama_kelas']); ?></div>
                            <div class="inline-block px-2 py-1 bg-rose-500/10 text-rose-400 text-[10px] font-bold rounded border border-rose-500/20">
                                +<?php echo $row['poin']; ?> Poin
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-slate-500">
                            <i class="fa-regular fa-face-smile text-3xl mb-2 block"></i>
                            <p class="text-sm">Belum ada pelanggaran tercatat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</body>
</html>