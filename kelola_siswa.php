<?php
// kelola_siswa.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan / Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $query = "INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif, created_at, updated_at) 
                  VALUES ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', '$status_aktif', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil ditambahkan!'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal simpan data: ' . mysqli_error($koneksi)];
        }
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_siswa SET nis='$nis', nisn='$nisn', nama='$nama', jenis_kelamin='$jenis_kelamin', 
                  tanggal_lahir='$tanggal_lahir', alamat='$alamat', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil diperbarui!'];
        }
    }
    header("Location: kelola_siswa.php");
    exit();
}

// Proses Hapus
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if (mysqli_query($koneksi, "DELETE FROM t_siswa WHERE id = '$id'")) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil dihapus!'];
    }
    header("Location: kelola_siswa.php");
    exit();
}

// Pencarian
$search = $_GET['search'] ?? '';
$where = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE nis LIKE '%$s%' OR nisn LIKE '%$s%' OR nama LIKE '%$s%'";
}

$query_data = "SELECT * FROM t_siswa $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Siswa - Sistem Kedisiplinan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { background-color: #0f172a; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; }
        .glass-card { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen p-6 md:p-10">

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <a href="dashboard_admin.php" class="text-indigo-400 hover:text-indigo-300 text-sm mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
                <h1 class="text-3xl font-bold text-white"><i class="fa-solid fa-user-graduate text-indigo-400 mr-2"></i> Data Siswa</h1>
            </div>
            <button onclick="openModal('create')" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-lg font-medium shadow-lg"><i class="fa-solid fa-plus"></i> Tambah Siswa</button>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm flex items-center justify-between border <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-emerald-900/50 text-emerald-300 border-emerald-800' : 'bg-rose-900/50 text-rose-300 border-rose-800'; ?>">
                <span><i class="fa-solid fa-info-circle mr-2"></i> <?php echo $_SESSION['flash']['message']; ?></span>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="glass-card p-4 rounded-xl mb-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari NIS, NISN, atau Nama Siswa..." class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-indigo-500">
                <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-2 rounded-lg">Cari</button>
                <?php if($search): ?><a href="kelola_siswa.php" class="bg-rose-900/50 text-rose-300 px-4 py-2 rounded-lg border border-rose-800 flex items-center"><i class="fa-solid fa-rotate-left"></i></a><?php endif; ?>
            </form>
        </div>

        <!-- Data Tabel -->
        <div class="glass-card rounded-xl overflow-hidden shadow-xl border border-slate-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/90 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">NIS / NISN</th>
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4">L/P</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-800/60">
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-indigo-400"><?php echo htmlspecialchars($data['nis']); ?></div>
                                        <div class="text-[11px] text-slate-500"><?php echo htmlspecialchars($data['nisn']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-white"><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td class="px-6 py-4"><?php echo $data['jenis_kelamin']; ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($data['status_aktif'] == 1): ?>
                                            <span class="bg-emerald-950/60 text-emerald-400 border border-emerald-800 px-2 py-1 rounded text-[11px]">Aktif</span>
                                        <?php else: ?>
                                            <span class="bg-slate-800 text-slate-400 border border-slate-700 px-2 py-1 rounded text-[11px]">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="px-3 py-1.5 bg-indigo-900/40 text-indigo-400 border border-indigo-800 rounded hover:bg-indigo-600 hover:text-white"><i class="fa-solid fa-pen"></i></button>
                                        <a href="kelola_siswa.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus siswa ini?')" class="px-3 py-1.5 bg-rose-900/40 text-rose-400 border border-rose-800 rounded hover:bg-rose-600 hover:text-white"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada data siswa.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="modalForm" class="fixed inset-0 bg-slate-950/80 z-50 flex items-center justify-center hidden p-4">
        <div class="glass-card max-w-lg w-full p-6 rounded-2xl">
            <h3 id="modalTitle" class="text-xl font-bold text-white mb-4 border-b border-slate-700 pb-3"><i class="fa-solid fa-user-plus text-indigo-400"></i> Tambah Siswa</h3>
            <form method="POST" action="kelola_siswa.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">NIS *</label>
                        <input type="text" name="nis" id="inputNis" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">NISN</label>
                        <input type="text" name="nisn" id="inputNisn" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs text-slate-400 mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama" id="inputNama" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" id="inputJk" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white">
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" id="inputTgl" required class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white [color-scheme:dark]">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs text-slate-400 mb-1">Alamat</label>
                    <textarea name="alamat" id="inputAlamat" rows="2" class="w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white"></textarea>
                </div>
                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked class="w-4 h-4 bg-slate-900">
                    <label class="text-sm text-slate-300">Status Aktif</label>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-700 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">Simpan Data</button>
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
                document.getElementById('inputNis').value = '';
                document.getElementById('inputNisn').value = '';
                document.getElementById('inputNama').value = '';
                document.getElementById('inputAlamat').value = '';
                document.getElementById('inputStatus').checked = true;
            }
        }
        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputNis').value = data.nis;
            document.getElementById('inputNisn').value = data.nisn;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputJk').value = data.jenis_kelamin;
            document.getElementById('inputTgl').value = data.tanggal_lahir;
            document.getElementById('inputAlamat').value = data.alamat;
            document.getElementById('inputStatus').checked = data.status_aktif == 1;
        }
        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>