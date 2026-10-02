<?php
// rekap_point.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Ambil data siswa beserta total poin pelanggarannya
$query = "SELECT s.id, s.nis, s.nama, 
          COALESCE(SUM(ps.poin), 0) as total_poin, 
          COUNT(ps.id) as jumlah_kasus
          FROM t_siswa s
          LEFT JOIN t_pelanggaran_siswa ps ON s.id = ps.siswa_id
          WHERE s.status_aktif = 1
          GROUP BY s.id
          ORDER BY total_poin DESC, s.nama ASC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Point Pelanggaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { background-color: #0f172a; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; }
        .glass-card { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="dashboard_admin.php" class="text-rose-400 hover:text-rose-300 text-sm mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
                <h1 class="text-3xl font-bold text-white"><i class="fa-solid fa-calculator text-rose-500 mr-2"></i> Rekap Point Siswa</h1>
            </div>
            <button onclick="window.print()" class="bg-slate-700 hover:bg-slate-600 text-white px-5 py-2.5 rounded-lg font-medium shadow-lg"><i class="fa-solid fa-print"></i> Cetak Halaman</button>
        </div>

        <div class="glass-card rounded-xl overflow-hidden shadow-xl border border-slate-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/90 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Peringkat</th>
                            <th class="px-6 py-4">NIS</th>
                            <th class="px-6 py-4">Nama Siswa</th>
                            <th class="px-6 py-4 text-center">Jumlah Kasus</th>
                            <th class="px-6 py-4 text-center">Total Point</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php 
                        $rank = 1;
                        while ($data = mysqli_fetch_assoc($result)): 
                            // Pewarnaan berdasarkan jumlah poin
                            $point_color = 'text-emerald-400';
                            if($data['total_poin'] > 50) $point_color = 'text-rose-500 font-bold';
                            elseif($data['total_poin'] > 20) $point_color = 'text-amber-400 font-bold';
                        ?>
                            <tr class="hover:bg-slate-800/60">
                                <td class="px-6 py-4 text-center font-bold text-slate-500">#<?php echo $rank++; ?></td>
                                <td class="px-6 py-4 font-mono text-indigo-400"><?php echo htmlspecialchars($data['nis']); ?></td>
                                <td class="px-6 py-4 font-medium text-white"><?php echo htmlspecialchars($data['nama']); ?></td>
                                <td class="px-6 py-4 text-center"><?php echo $data['jumlah_kasus']; ?> x</td>
                                <td class="px-6 py-4 text-center text-lg <?php echo $point_color; ?>">
                                    <?php echo $data['total_poin']; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="riwayat.php?siswa_id=<?php echo $data['id']; ?>" class="px-3 py-1.5 bg-slate-800 border border-slate-600 text-slate-300 rounded hover:bg-slate-700 transition text-xs">
                                        Lihat Detail <i class="fa-solid fa-arrow-right ml-1"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>