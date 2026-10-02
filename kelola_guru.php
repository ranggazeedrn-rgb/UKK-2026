<?php
// kelola_guru.php
session_start();
include 'config/koneksi.php'; // Pastikan path ini benar sesuai foldermu
include 'cek_akses.php';

// Proses Simpan / Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        // 1. Buat akun login untuk Guru (t_users)
        $password = 'guru123'; // Password default
        
        // FIX: Tambahkan remember_token dan email_verified_at agar tidak error NOT NULL di MySQL
        $query_user = "INSERT INTO t_users (name, email, password, email_verified_at, remember_token, role, created_at, updated_at) 
                       VALUES ('$nama', '$email', '$password', NOW(), '', 'guru', NOW(), NOW())";
        
        if (mysqli_query($koneksi, $query_user)) {
            $new_user_id = mysqli_insert_id($koneksi); // Ambil ID user yang baru dibuat
            
            // 2. Simpan biodata ke t_guru
            $query_guru = "INSERT INTO t_guru (nip, nama, email, status_aktif, user_id, created_at, updated_at) 
                           VALUES ('$nip', '$nama', '$email', '$status_aktif', '$new_user_id', NOW(), NOW())";
            
            if (mysqli_query($koneksi, $query_guru)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Guru berhasil ditambahkan! Password login default: guru123'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal simpan biodata: ' . mysqli_error($koneksi)];
            }
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal membuat akun user: ' . mysqli_error($koneksi)];
        }
    } elseif ($action === 'update' && $id && $user_id) {
        // Update biodata guru
        $query_guru = "UPDATE t_guru SET nip = '$nip', nama = '$nama', email = '$email', status_aktif = '$status_aktif', updated_at = NOW() WHERE id = '$id'";
        // Update nama/email di akun login
        $query_user = "UPDATE t_users SET name = '$nama', email = '$email', updated_at = NOW() WHERE id = '$user_id'";
        
        if (mysqli_query($koneksi, $query_guru) && mysqli_query($koneksi, $query_user)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Guru berhasil diperbarui!'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . mysqli_error($koneksi)];
        }
    }
    header("Location: kelola_guru.php");
    exit();
}

// Proses Hapus Data
if (isset($_GET['delete_user_id'])) {
    $del_user_id = (int)$_GET['delete_user_id'];
    // Hapus dari t_users otomatis akan menghapus t_guru karena Constraint ON DELETE CASCADE di database
    $query = "DELETE FROM t_users WHERE id = '$del_user_id' AND role = 'guru'";
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Guru beserta akun login berhasil dihapus!'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus: ' . mysqli_error($koneksi)];
    }
    header("Location: kelola_guru.php");
    exit();
}

// Pencarian Filter
$search = $_GET['search'] ?? '';
$where = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE nip LIKE '%$s%' OR nama LIKE '%$s%' OR email LIKE '%$s%'";
}

// Ambil Data Guru
$query_data = "SELECT * FROM t_guru $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query_data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Guru - Sistem Kedisiplinan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { background-color: #0f172a; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; }
        .glass-card { background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen p-6 md:p-10 font-sans">

    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <a href="dashboard_admin.php" class="text-indigo-400 hover:text-indigo-300 text-sm mb-2 inline-block transition-colors"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Dashboard</a>
                <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                    <i class="fa-solid fa-user-tie text-emerald-400"></i> Kelola Data Guru
                </h1>
            </div>
            <button onclick="openModal('create')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Guru
            </button>
        </div>

        <!-- Flash Message Notification -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-xl text-sm flex items-center justify-between border <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-emerald-900/50 text-emerald-300 border-emerald-800' : 'bg-rose-900/50 text-rose-300 border-rose-800'; ?> shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400'; ?>">
                        <i class="fa-solid <?php echo $_SESSION['flash']['type'] === 'success' ? 'fa-check' : 'fa-xmark'; ?>"></i>
                    </div>
                    <span class="font-medium"><?php echo $_SESSION['flash']['message']; ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-times text-lg"></i></button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="glass-card p-4 rounded-xl mb-6 shadow-md">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-slate-400"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari NIP, Nama, atau Email Guru..." class="w-full pl-11 pr-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-2.5 rounded-lg text-sm transition font-medium w-full sm:w-auto">Cari Data</button>
                    <?php if($search): ?>
                        <a href="kelola_guru.php" class="bg-rose-900/50 hover:bg-rose-900/80 text-rose-300 px-4 py-2.5 rounded-lg text-sm border border-rose-800 flex items-center transition w-full sm:w-auto text-center justify-center" title="Reset Pencarian">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="glass-card rounded-xl overflow-hidden shadow-xl border border-slate-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/90 text-slate-400 uppercase text-xs border-b border-slate-700 font-semibold tracking-wider">
                        <tr>
                            <th class="px-6 py-5">NIP</th>
                            <th class="px-6 py-5">Nama Lengkap</th>
                            <th class="px-6 py-5">Email (Akun Login)</th>
                            <th class="px-6 py-5 text-center">Status</th>
                            <th class="px-6 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-800/60 transition-colors duration-200">
                                    <td class="px-6 py-4 font-mono text-emerald-400 font-medium"><?php echo htmlspecialchars($data['nip']); ?></td>
                                    <td class="px-6 py-4 font-medium text-white"><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td class="px-6 py-4 text-slate-400"><?php echo htmlspecialchars($data['email']); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($data['status_aktif'] == 1): ?>
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-950/60 text-emerald-400 border border-emerald-800/60 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 bg-slate-800/80 text-slate-400 border border-slate-700 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Non-Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="w-9 h-9 flex items-center justify-center bg-indigo-900/40 text-indigo-400 border border-indigo-800/50 hover:bg-indigo-600 hover:text-white rounded-lg transition-all" title="Edit Biodata">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <a href="kelola_guru.php?delete_user_id=<?php echo $data['user_id']; ?>" onclick="return confirm('PERINGATAN: Menghapus guru ini juga akan menghapus akun loginnya! Lanjutkan?')" class="w-9 h-9 flex items-center justify-center bg-rose-900/40 text-rose-400 border border-rose-800/50 hover:bg-rose-600 hover:text-white rounded-lg transition-all" title="Hapus Permanen">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center text-slate-500">
                                    <div class="w-16 h-16 mx-auto bg-slate-800 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-user-slash text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada data guru yang ditambahkan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit -->
    <div id="modalForm" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4 transition-opacity">
        <div class="glass-card max-w-md w-full p-6 rounded-2xl shadow-2xl border border-slate-700/50 transform transition-transform">
            <div class="flex justify-between items-center pb-4 border-b border-slate-700 mb-5">
                <h3 id="modalTitle" class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-emerald-400"></i> <span>Tambah Guru</span>
                </h3>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-800 text-slate-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form method="POST" action="kelola_guru.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">
                <input type="hidden" name="user_id" id="dataUserId">

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold tracking-wide text-slate-400 mb-1.5">NIP / ID PEGAWAI <span class="text-rose-500">*</span></label>
                        <input type="text" name="nip" id="inputNip" required placeholder="Contoh: 19800101..." class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-wide text-slate-400 mb-1.5">NAMA LENGKAP <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" id="inputNama" required placeholder="Nama beserta gelar..." class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-wide text-slate-400 mb-1.5">EMAIL AKUN <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="inputEmail" required placeholder="guru@sekolah.com" class="w-full px-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm transition-all">
                    </div>
                    <div class="flex items-center gap-3 pt-3">
                        <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-0 focus:ring-offset-0 cursor-pointer">
                        <label for="inputStatus" class="text-sm font-medium text-slate-300 cursor-pointer select-none">Status Guru Aktif Mengajar</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-5 border-t border-slate-700">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-900/50 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(mode) {
            document.getElementById('modalForm').classList.remove('hidden');
            if (mode === 'create') {
                document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-plus text-emerald-400"></i> <span>Tambah Guru</span>';
                document.getElementById('formAction').value = 'create';
                document.getElementById('dataId').value = '';
                document.getElementById('dataUserId').value = '';
                document.getElementById('inputNip').value = '';
                document.getElementById('inputNama').value = '';
                document.getElementById('inputEmail').value = '';
                document.getElementById('inputStatus').checked = true;
            }
        }

        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-pen text-indigo-400"></i> <span>Edit Biodata Guru</span>';
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('dataUserId').value = data.user_id;
            document.getElementById('inputNip').value = data.nip;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputEmail').value = data.email;
            document.getElementById('inputStatus').checked = parseInt(data.status_aktif) === 1;
        }

        function closeModal() {
            document.getElementById('modalForm').classList.add('hidden');
        }
    </script>
</body>
</html>