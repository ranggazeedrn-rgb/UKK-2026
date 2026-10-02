<?php
// kelola_jenis_pelanggaran.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan / Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $pelanggaran_kategori_id = $_POST['pelanggaran_kategori_id'];
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $poin = (int)$_POST['poin'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $query = "INSERT INTO t_pelanggaran (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif, created_at, updated_at) 
                  VALUES ('$pelanggaran_kategori_id', '$kode', '$nama', '$poin', '$deskripsi', '$status_aktif', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data jenis pelanggaran berhasil ditambahkan!'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menambahkan data: ' . mysqli_error($koneksi)];
        }
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_pelanggaran SET 
                  pelanggaran_kategori_id = '$pelanggaran_kategori_id',
                  kode = '$kode',
                  nama = '$nama',
                  poin = '$poin',
                  deskripsi = '$deskripsi',
                  status_aktif = '$status_aktif',
                  updated_at = NOW()
                  WHERE id = '$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data jenis pelanggaran berhasil diperbarui!'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui data: ' . mysqli_error($koneksi)];
        }
    }
    header("Location: kelola_jenis_pelanggaran.php");
    exit();
}

// Proses Hapus Data
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $query = "DELETE FROM t_pelanggaran WHERE id = '$delete_id'";
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data pelanggaran berhasil dihapus!'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data: ' . mysqli_error($koneksi)];
    }
    header("Location: kelola_jenis_pelanggaran.php");
    exit();
}

// Pencarian & Filter
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori_id'] ?? '';

$where_clauses = [];
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where_clauses[] = "(p.kode LIKE '%$s%' OR p.nama LIKE '%$s%' OR p.deskripsi LIKE '%$s%')";
}
if (!empty($kategori_filter)) {
    $where_clauses[] = "p.pelanggaran_kategori_id = '$kategori_filter'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";

// Query Data Pelanggaran + Kategori
$query_data = "SELECT p.*, k.nama AS nama_kategori 
               FROM t_pelanggaran p 
               LEFT JOIN t_pelanggaran_kategori k ON p.pelanggaran_kategori_id = k.id 
               $where_sql 
               ORDER BY p.id DESC";
$result = mysqli_query($koneksi, $query_data);

// Fetch Data Kategori untuk Dropdown
$kategori_res = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE status_aktif = 1 ORDER BY nama ASC");
$kategori_list = [];
while ($row = mysqli_fetch_assoc($kategori_res)) {
    $kategori_list[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jenis Pelanggaran - Sistem Kedisiplinan</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        darkbg: '#0f172a',
                        cardbg: '#1e293b',
                        borderbg: '#334155'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-darkbg text-slate-100 font-sans min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">
        
        <!-- Main Content Area -->
        <main class="flex-1 p-6 md:p-10">
            
            <!-- Title Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Kelola Jenis Pelanggaran
                    </h1>
                    <p class="text-slate-400 text-sm mt-1">Kelola data master kode, nama, dan bobot poin pelanggaran siswa.</p>
                </div>
                <button onclick="openModal('create')" class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-4 py-2.5 rounded-lg shadow-lg flex items-center gap-2 transition duration-200">
                    <i class="fa-solid fa-plus"></i> Tambah Pelanggaran
                </button>
            </div>

            <!-- Flash Message Alert -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="p-4 mb-6 rounded-lg text-sm flex items-center justify-between border <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-emerald-950/80 text-emerald-300 border-emerald-800' : 'bg-rose-950/80 text-rose-300 border-rose-800'; ?>">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid <?php echo $_SESSION['flash']['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                        <span><?php echo $_SESSION['flash']['message']; ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">&times;</button>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Filter & Search Bar -->
            <div class="bg-cardbg border border-borderbg rounded-xl p-4 mb-6">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400"></i>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Kode, Nama, atau Deskripsi..." class="w-full pl-10 pr-4 py-2 bg-slate-900 border border-borderbg rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 text-sm">
                    </div>
                    <div class="w-full md:w-64">
                        <select name="kategori_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-900 border border-borderbg rounded-lg text-white text-sm focus:outline-none focus:border-indigo-500">
                            <option value="">-- Semua Kategori --</option>
                            <?php foreach ($kategori_list as $kat): ?>
                                <option value="<?php echo $kat['id']; ?>" <?php echo $kategori_filter == $kat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kat['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm transition">Cari</button>
                        <?php if(!empty($search) || !empty($kategori_filter)): ?>
                            <a href="kelola_jenis_pelanggaran.php" class="bg-rose-900/50 hover:bg-rose-800 text-rose-300 px-3 py-2 rounded-lg text-sm flex items-center gap-1 border border-rose-800">
                                <i class="fa-solid fa-rotate-left"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table Data Pelanggaran -->
            <div class="bg-cardbg border border-borderbg rounded-xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/80 text-slate-400 uppercase text-xs border-b border-borderbg">
                            <tr>
                                <th class="px-6 py-4">Kode</th>
                                <th class="px-6 py-4">Nama Pelanggaran</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Poin</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-borderbg">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                    <tr class="hover:bg-slate-800/50 transition">
                                        <td class="px-6 py-4 font-mono font-bold text-indigo-400">
                                            <?php echo htmlspecialchars($data['kode']); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-white"><?php echo htmlspecialchars($data['nama']); ?></div>
                                            <div class="text-xs text-slate-400 mt-0.5 line-clamp-1"><?php echo htmlspecialchars($data['deskripsi']); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-slate-800 text-slate-300 px-2.5 py-1 rounded-md text-xs border border-slate-700">
                                                <?php echo htmlspecialchars($data['nama_kategori'] ?? 'Tanpa Kategori'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-amber-950/60 text-amber-400 border border-amber-800 font-semibold px-2.5 py-1 rounded-md text-xs">
                                                +<?php echo $data['poin']; ?> Poin
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($data['status_aktif'] == 1): ?>
                                                <span class="inline-flex items-center gap-1.5 bg-emerald-950/60 text-emerald-400 border border-emerald-800 px-2.5 py-1 rounded-full text-xs font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 bg-slate-800 text-slate-400 border border-slate-700 px-2.5 py-1 rounded-full text-xs font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Non-Aktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="p-2 bg-indigo-950/60 text-indigo-400 border border-indigo-800 hover:bg-indigo-900 rounded-lg transition" title="Edit Data">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <a href="kelola_jenis_pelanggaran.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggaran ini?')" class="p-2 bg-rose-950/60 text-rose-400 border border-rose-800 hover:bg-rose-900 rounded-lg transition" title="Hapus Data">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                        <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>
                                        Belum ada data jenis pelanggaran yang ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Form (Tambah / Edit) -->
    <div id="modalForm" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-cardbg border border-borderbg rounded-xl max-w-lg w-full p-6 shadow-2xl">
            <div class="flex justify-between items-center pb-4 border-b border-borderbg mb-4">
                <h3 id="modalTitle" class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-plus text-indigo-400"></i> Tambah Pelanggaran
                </h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>

            <form method="POST" action="kelola_jenis_pelanggaran.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Kode Pelanggaran *</label>
                            <input type="text" name="kode" id="inputKode" required placeholder="Contoh: P01" class="w-full px-3 py-2 bg-slate-900 border border-borderbg rounded-lg text-white focus:outline-none focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Bobot Poin *</label>
                            <input type="number" name="poin" id="inputPoin" min="1" required placeholder="10" class="w-full px-3 py-2 bg-slate-900 border border-borderbg rounded-lg text-white focus:outline-none focus:border-indigo-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Kategori Pelanggaran *</label>
                        <select name="pelanggaran_kategori_id" id="inputKategori" required class="w-full px-3 py-2 bg-slate-900 border border-borderbg rounded-lg text-white focus:outline-none focus:border-indigo-500 text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori_list as $kat): ?>
                                <option value="<?php echo $kat['id']; ?>"><?php echo htmlspecialchars($kat['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Nama Pelanggaran *</label>
                        <input type="text" name="nama" id="inputNama" required placeholder="Contoh: Terlambat Masuk Sekolah" class="w-full px-3 py-2 bg-slate-900 border border-borderbg rounded-lg text-white focus:outline-none focus:border-indigo-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Deskripsi / Detail</label>
                        <textarea name="deskripsi" id="inputDeskripsi" rows="3" placeholder="Keterangan singkat mengenai tata tertib pelanggaran ini..." class="w-full px-3 py-2 bg-slate-900 border border-borderbg rounded-lg text-white focus:outline-none focus:border-indigo-500 text-sm"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked class="w-4 h-4 rounded border-borderbg bg-slate-900 text-indigo-600 focus:ring-0">
                        <label for="inputStatus" class="text-sm text-slate-300">Status Aktif</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-borderbg">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-sm transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Modal Logic -->
    <script>
        function openModal(mode) {
            document.getElementById('modalForm').classList.remove('hidden');
            if (mode === 'create') {
                document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-circle-plus text-indigo-400"></i> Tambah Pelanggaran';
                document.getElementById('formAction').value = 'create';
                document.getElementById('dataId').value = '';
                document.getElementById('inputKode').value = '';
                document.getElementById('inputPoin').value = '';
                document.getElementById('inputKategori').value = '';
                document.getElementById('inputNama').value = '';
                document.getElementById('inputDeskripsi').value = '';
                document.getElementById('inputStatus').checked = true;
            }
        }

        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-indigo-400"></i> Edit Pelanggaran';
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputKode').value = data.kode;
            document.getElementById('inputPoin').value = data.poin;
            document.getElementById('inputKategori').value = data.pelanggaran_kategori_id;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputDeskripsi').value = data.deskripsi;
            document.getElementById('inputStatus').checked = parseInt(data.status_aktif) === 1;
        }

        function closeModal() {
            document.getElementById('modalForm').classList.add('hidden');
        }
    </script>
</body>
</html>