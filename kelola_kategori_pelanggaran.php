<?php
// kelola_kategori_pelanggaran.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $query = "INSERT INTO t_pelanggaran_kategori (nama, status_aktif, created_at, updated_at) VALUES ('$nama', '$status_aktif', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori Pelanggaran berhasil ditambahkan!'];
        }
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_pelanggaran_kategori SET nama='$nama', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori Pelanggaran berhasil diperbarui!'];
        }
    }
    header("Location: kelola_kategori_pelanggaran.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if (mysqli_query($koneksi, "DELETE FROM t_pelanggaran_kategori WHERE id = '$id'")) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori Pelanggaran berhasil dihapus!'];
    }
    header("Location: kelola_kategori_pelanggaran.php");
    exit();
}

$query_data = "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC";
$result = mysqli_query($koneksi, $query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori Pelanggaran</title>
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
                <a href="dashboard_admin.php" class="text-amber-400 hover:text-amber-300 text-sm mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
                <h1 class="text-3xl font-bold text-white"><i class="fa-solid fa-tags text-amber-400 mr-2"></i> Kategori Pelanggaran</h1>
            </div>
            <button onclick="openModal('create')" class="bg-amber-600 hover:bg-amber-500 text-white px-5 py-2.5 rounded-lg font-medium"><i class="fa-solid fa-plus"></i> Tambah Kategori</button>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm border bg-emerald-900/50 text-emerald-300 border-emerald-800">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="glass-card rounded-xl overflow-hidden shadow-xl border border-slate-700/50">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    <?php while ($data = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-slate-800/60">
                            <td class="px-6 py-4 font-bold text-white"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php echo $data['status_aktif'] == 1 ? '<span class="text-emerald-400">Aktif</span>' : '<span class="text-slate-500">Non-Aktif</span>'; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="px-3 py-1.5 bg-indigo-900/40 text-indigo-400 rounded hover:bg-indigo-600 hover:text-white"><i class="fa-solid fa-pen"></i></button>
                                <a href="kelola_kategori_pelanggaran.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus kategori?')" class="px-3 py-1.5 bg-rose-900/40 text-rose-400 rounded hover:bg-rose-600 hover:text-white"><i class="fa-solid fa-trash"></i></a>
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
            <h3 class="text-xl font-bold text-white mb-4"><i class="fa-solid fa-tag text-amber-400"></i> Form Kategori</h3>
            <form method="POST" action="kelola_kategori_pelanggaran.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">
                <div class="mb-4">
                    <label class="block text-xs text-slate-400 mb-1">Nama Kategori (Misal: Kerajinan, Sikap)</label>
                    <input type="text" name="nama" id="inputNama" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                </div>
                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked>
                    <label class="text-sm text-slate-300">Status Aktif</label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(mode) {
            document.getElementById('modalForm').classList.remove('hidden');
            if(mode === 'create') {
                document.getElementById('formAction').value = 'create';
                document.getElementById('dataId').value = '';
                document.getElementById('inputNama').value = '';
                document.getElementById('inputStatus').checked = true;
            }
        }
        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputStatus').checked = data.status_aktif == 1;
        }
        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>