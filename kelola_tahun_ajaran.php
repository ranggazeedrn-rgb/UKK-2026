<?php
// kelola_tahun_ajaran.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $tahun_ajaran = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran']);
    $semester = mysqli_real_escape_string($koneksi, $_POST['semester']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    // Jika diset aktif, nonaktifkan tahun ajaran lainnya
    if ($status_aktif == 1) {
        mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET status_aktif = 0");
    }

    if ($action === 'create') {
        $query = "INSERT INTO t_tahun_ajaran (tahun_ajaran, semester, status_aktif, created_at, updated_at) 
                  VALUES ('$tahun_ajaran', '$semester', '$status_aktif', NOW(), NOW())";
        mysqli_query($koneksi, $query);
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_tahun_ajaran SET tahun_ajaran='$tahun_ajaran', semester='$semester', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        mysqli_query($koneksi, $query);
    }
    header("Location: kelola_tahun_ajaran.php");
    exit();
}

$result = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Tahun Ajaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { background-color: #0f172a; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; }
        .glass-card { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen p-6 md:p-10">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="dashboard_admin.php" class="text-indigo-400 hover:text-indigo-300 text-sm mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
                <h1 class="text-3xl font-bold text-white"><i class="fa-solid fa-calendar-days text-indigo-400 mr-2"></i> Tahun Ajaran</h1>
            </div>
            <button onclick="openModal()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-lg font-medium"><i class="fa-solid fa-plus"></i> Tambah Tahun Ajaran</button>
        </div>

        <div class="glass-card rounded-xl overflow-hidden shadow-xl border border-slate-700/50">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Tahun Ajaran</th>
                        <th class="px-6 py-4">Semester</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php while ($data = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-slate-800/60">
                            <td class="px-6 py-4 font-bold text-white"><?php echo htmlspecialchars($data['tahun_ajaran']); ?></td>
                            <td class="px-6 py-4 capitalize"><?php echo htmlspecialchars($data['semester']); ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php echo $data['status_aktif'] == 1 ? '<span class="bg-emerald-950 text-emerald-400 border border-emerald-800 px-3 py-1 rounded-full text-xs font-bold">AKTIF</span>' : '<span class="text-slate-500 text-xs">Non-Aktif</span>'; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="modalForm" class="fixed inset-0 bg-slate-950/80 z-50 flex items-center justify-center hidden p-4">
        <div class="glass-card max-w-md w-full p-6 rounded-2xl">
            <h3 class="text-xl font-bold text-white mb-4">Tambah Tahun Ajaran</h3>
            <form method="POST" action="kelola_tahun_ajaran.php">
                <input type="hidden" name="action" value="create">
                <div class="mb-4">
                    <label class="block text-xs text-slate-400 mb-1">Tahun Ajaran (Contoh: 2025/2026)</label>
                    <input type="text" name="tahun_ajaran" required placeholder="2025/2026" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                </div>
                <div class="mb-4">
                    <label class="block text-xs text-slate-400 mb-1">Semester</label>
                    <select name="semester" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" value="1" checked>
                    <label class="text-sm text-slate-300">Set Sebagai Tahun Ajaran Aktif</label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('modalForm').classList.remove('hidden'); }
        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>