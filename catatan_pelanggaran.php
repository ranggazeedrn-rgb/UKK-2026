<?php
// catatan_pelanggaran.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan Transaksi Pelanggaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = (int)$_POST['siswa_id'];
    $pelanggaran_id = (int)$_POST['pelanggaran_id'];
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $tindakan = mysqli_real_escape_string($koneksi, $_POST['tindakan'] ?? 'Diberi peringatan');
    $status = 'Diproses';

    // 1. Ambil detail data Siswa & Kelasnya
    $q_siswa = mysqli_query($koneksi, "SELECT s.nama as nama_siswa, k.id as kelas_id, k.nama as nama_kelas 
                                       FROM t_siswa s 
                                       LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id 
                                       LEFT JOIN t_kelas k ON ks.kelas_id = k.id 
                                       WHERE s.id = '$siswa_id' LIMIT 1");
    $d_siswa = mysqli_fetch_assoc($q_siswa);
    $nama_siswa = mysqli_real_escape_string($koneksi, $d_siswa['nama_siswa'] ?? 'Siswa');
    $kelas_id = $d_siswa['kelas_id'] ? "'".$d_siswa['kelas_id']."'" : "NULL";
    $nama_kelas = mysqli_real_escape_string($koneksi, $d_siswa['nama_kelas'] ?? '-');

    // 2. Ambil detail Pelanggaran & Kategori
    $q_pelanggaran = mysqli_query($koneksi, "SELECT nama, poin, pelanggaran_kategori_id FROM t_pelanggaran WHERE id = '$pelanggaran_id'");
    $d_pelanggaran = mysqli_fetch_assoc($q_pelanggaran);
    $nama_pelanggaran = mysqli_real_escape_string($koneksi, $d_pelanggaran['nama'] ?? 'Pelanggaran');
    $poin = (int)($d_pelanggaran['poin'] ?? 0);
    $pelanggaran_kategori_id = $d_pelanggaran['pelanggaran_kategori_id'] ? "'".$d_pelanggaran['pelanggaran_kategori_id']."'" : "NULL";

    // 3. Ambil Tahun Ajaran Aktif
    $q_ta = mysqli_query($koneksi, "SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
    $d_ta = mysqli_fetch_assoc($q_ta);
    $tahun_ajaran_id = $d_ta['id'] ? "'".$d_ta['id']."'" : "NULL";

    // 4. Ambil Guru Pelapor dari session user
    $user_id = $_SESSION['user_id'];
    $q_guru = mysqli_query($koneksi, "SELECT id, nama FROM t_guru WHERE user_id = '$user_id' LIMIT 1");
    if (mysqli_num_rows($q_guru) > 0) {
        $d_guru = mysqli_fetch_assoc($q_guru);
        $guru_id = "'".$d_guru['id']."'";
        $nama_guru = mysqli_real_escape_string($koneksi, $d_guru['nama']);
    } else {
        $guru_id = "NULL";
        $nama_guru = mysqli_real_escape_string($koneksi, $_SESSION['name'] ?? 'Administrator');
    }

    // Insert Sesuai Kolom Tabel t_pelanggaran_siswa
    $query = "INSERT INTO t_pelanggaran_siswa 
              (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status, created_at, updated_at) 
              VALUES 
              ($tahun_ajaran_id, '$siswa_id', '$nama_siswa', $kelas_id, '$nama_kelas', '$pelanggaran_id', '$nama_pelanggaran', $pelanggaran_kategori_id, $guru_id, '$nama_guru', '$tanggal', '$keterangan', '$poin', '$tindakan', '$status', NOW(), NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Catatan pelanggaran siswa berhasil disimpan!'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)];
    }
    header("Location: catatan_pelanggaran.php");
    exit();
}

// Ambil Data Master Dropdown Form
$siswa_list = mysqli_query($koneksi, "SELECT id, nis, nama FROM t_siswa WHERE status_aktif = 1 ORDER BY nama ASC");
$pelanggaran_list = mysqli_query($koneksi, "SELECT p.id, p.kode, p.nama, p.poin, k.nama as kategori 
                                            FROM t_pelanggaran p 
                                            LEFT JOIN t_pelanggaran_kategori k ON p.pelanggaran_kategori_id = k.id 
                                            WHERE p.status_aktif = 1 ORDER BY k.nama ASC, p.kode ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Catatan Pelanggaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { background-color: #0f172a; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; }
        .glass-card { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen p-6 md:p-10 flex items-center justify-center">

    <div class="glass-card max-w-2xl w-full p-8 rounded-2xl shadow-2xl border border-indigo-900/50">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-700">
            <div>
                <a href="dashboard_admin.php" class="text-indigo-400 hover:text-indigo-300 text-sm mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Dashboard</a>
                <h1 class="text-2xl font-bold text-white"><i class="fa-solid fa-clipboard-list text-indigo-500 mr-2"></i> Input Pelanggaran Siswa</h1>
            </div>
            <a href="riwayat.php" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-sm border border-slate-600 transition">Lihat Riwayat</a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm border <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-emerald-900/50 text-emerald-300 border-emerald-800' : 'bg-rose-900/50 text-rose-300 border-rose-800'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="catatan_pelanggaran.php" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Tanggal Kejadian <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white [color-scheme:dark] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="siswa_id" required class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">-- Pilih Siswa --</option>
                    <?php while ($s = mysqli_fetch_assoc($siswa_list)): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Jenis Pelanggaran <span class="text-rose-500">*</span></label>
                <select name="pelanggaran_id" required class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">-- Pilih Jenis Pelanggaran --</option>
                    <?php 
                    $current_kategori = '';
                    while ($p = mysqli_fetch_assoc($pelanggaran_list)): 
                        if ($current_kategori != $p['kategori']) {
                            if ($current_kategori != '') echo '</optgroup>';
                            $current_kategori = $p['kategori'];
                            echo '<optgroup label="Kategori: ' . htmlspecialchars($current_kategori) . '">';
                        }
                    ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['kode'] . ' - ' . $p['nama']); ?> (+<?php echo $p['poin']; ?> Poin)
                        </option>
                    <?php endwhile; echo '</optgroup>'; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Deskripsi / Keterangan Kejadian</label>
                <textarea name="catatan" rows="2" placeholder="Keterangan detail kejadian..." class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Tindakan Penanganan</label>
                <input type="text" name="tindakan" value="Diberi teguran & pembinaan" placeholder="Tindakan yang diberikan..." class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-lg shadow-lg shadow-indigo-900/50 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Pelanggaran
            </button>
        </form>
    </div>

</body>
</html>