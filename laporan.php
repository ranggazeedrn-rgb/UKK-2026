<?php
// laporan.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');

$query = "SELECT ps.*, s.nis, s.nama as nama_siswa, p.nama as nama_pelanggaran 
          FROM t_pelanggaran_siswa ps
          JOIN t_siswa s ON ps.siswa_id = s.id
          JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          WHERE ps.tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'
          ORDER BY ps.tanggal ASC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kedisiplinan Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { background-color: #0f172a; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; }
        .glass-card { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .glass-card { border: none !important; background: transparent !important; box-shadow: none !important; }
            table { color: black !important; border-collapse: collapse !important; width: 100% !important; }
            th, td { border: 1px solid #000 !important; padding: 8px !important; }
        }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        
        <div class="no-print flex justify-between items-center mb-8">
            <div>
                <a href="dashboard_admin.php" class="text-indigo-400 hover:text-indigo-300 text-sm mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
                <h1 class="text-3xl font-bold text-white"><i class="fa-solid fa-print text-indigo-400 mr-2"></i> Cetak Laporan</h1>
            </div>
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-lg font-bold shadow-lg"><i class="fa-solid fa-print mr-2"></i> Print Laporan</button>
        </div>

        <!-- Filter Tanggal -->
        <div class="no-print glass-card p-4 rounded-xl mb-6">
            <form method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" value="<?php echo $tgl_mulai; ?>" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-sm [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" value="<?php echo $tgl_selesai; ?>" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-sm [color-scheme:dark]">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-medium">Filter Laporan</button>
            </form>
        </div>

        <!-- Area Laporan Cetak -->
        <div class="glass-card p-8 rounded-xl shadow-xl">
            <div class="text-center mb-6 pb-4 border-b border-slate-700">
                <h2 class="text-2xl font-bold uppercase tracking-wider">Laporan Rekapitulasi Pelanggaran Siswa</h2>
                <p class="text-sm text-slate-400 mt-1">Periode: <?php echo date('d/m/Y', strtotime($tgl_mulai)); ?> s/d <?php echo date('d/m/Y', strtotime($tgl_selesai)); ?></p>
            </div>

            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900/80 uppercase text-xs border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">NIS</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">Pelanggaran</th>
                        <th class="px-4 py-3 text-center">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php 
                    $no = 1; 
                    $total_poin_periode = 0;
                    if (mysqli_num_rows($result) > 0): 
                        while ($row = mysqli_fetch_assoc($result)): 
                            $total_poin_periode += $row['poin'];
                    ?>
                        <tr>
                            <td class="px-4 py-3 text-center"><?php echo $no++; ?></td>
                            <td class="px-4 py-3"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td class="px-4 py-3 font-mono"><?php echo htmlspecialchars($row['nis']); ?></td>
                            <td class="px-4 py-3 font-medium"><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($row['nama_pelanggaran']); ?></td>
                            <td class="px-4 py-3 text-center font-bold text-rose-400">+<?php echo $row['poin']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                        <tr class="font-bold bg-slate-900/50">
                            <td colspan="5" class="px-4 py-3 text-right">TOTAL POIN:</td>
                            <td class="px-4 py-3 text-center text-rose-400"><?php echo $total_poin_periode; ?></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Tidak ada data transaksi pelanggaran pada periode ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>