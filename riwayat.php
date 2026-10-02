<?php
// riwayat.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$filter_siswa = $_GET['siswa_id'] ?? '';
$where = "";
if (!empty($filter_siswa)) {
    $s_id = (int)$filter_siswa;
    $where = "WHERE ps.siswa_id = '$s_id'";
}

// Query disesuaikan dengan struktur tabel t_pelanggaran_siswa (menggunakan ps.nama_guru langsung)
$query = "SELECT ps.*, s.nis 
          FROM t_pelanggaran_siswa ps
          LEFT JOIN t_siswa s ON ps.siswa_id = s.id
          $where
          ORDER BY ps.tanggal DESC, ps.id DESC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pelanggaran Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { background-color: #0f172a; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; }
        .glass-card { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen p-6 md:p-10 font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="dashboard_admin.php" class="text-indigo-400 hover:text-indigo-300 text-sm mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Dashboard</a>
                <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-400"></i> Riwayat Pelanggaran
                </h1>
            </div>
            <?php if (!empty($filter_siswa)): ?>
                <a href="riwayat.php" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm transition"><i class="fa-solid fa-filter-circle-xmark mr-1"></i> Tampilkan Semua Siswa</a>
            <?php endif; ?>
        </div>

        <div class="glass-card rounded-xl overflow-hidden shadow-xl border border-slate-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/90 text-slate-400 uppercase text-xs font-semibold border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Siswa & Kelas</th>
                            <th class="px-6 py-4">Jenis Pelanggaran</th>
                            <th class="px-6 py-4 text-center">Poin</th>
                            <th class="px-6 py-4">Guru Pelapor</th>
                            <th class="px-6 py-4">Tindakan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="px-6 py-4 font-mono text-xs text-indigo-400">
                                        <?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-white"><?php echo htmlspecialchars($row['nama_siswa']); ?></div>
                                        <div class="text-xs text-slate-400">NIS: <?php echo htmlspecialchars($row['nis'] ?? '-'); ?> | Kelas: <?php echo htmlspecialchars($row['nama_kelas']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-amber-300">
                                        <?php echo htmlspecialchars($row['nama_pelanggaran']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-rose-950/60 text-rose-400 border border-rose-800 px-2.5 py-1 rounded-md font-bold text-xs">
                                            +<?php echo $row['poin']; ?> Poin
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-300">
                                        <i class="fa-solid fa-user-tie text-slate-500 mr-1"></i> <?php echo htmlspecialchars($row['nama_guru']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="text-slate-200 font-medium"><?php echo htmlspecialchars($row['tindakan']); ?></div>
                                        <div class="text-slate-400 italic text-[11px] mt-0.5"><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>
                                    Belum ada data riwayat pelanggaran tercatat.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>