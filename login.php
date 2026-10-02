<?php
// login.php
session_start();
include 'config/koneksi.php'; // Menggunakan jalur koneksi folder config

// Jika pengguna sudah login, alihkan langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard_admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // FIX 1: Periksa apakah key 'email' dan 'password' ada di $_POST sebelum diakses
    $email_input = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_input = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email_input) && !empty($password_input)) {
        $email = mysqli_real_escape_string($koneksi, $email_input);

        // FIX 2: Gunakan LOWER() agar pencocokan email tidak sensitif terhadap huruf besar/kecil
        $query = "SELECT * FROM t_users WHERE LOWER(email) = LOWER('$email') LIMIT 1";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Periksa password (mendukung password plain-text maupun password_hash)
            if ($password_input === $user['password'] || password_verify($password_input, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = $user['role'];

                header("Location: dashboard_admin.php");
                exit();
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Email tidak terdaftar dalam sistem!";
        }
    } else {
        $error = "Silakan isi email dan password terlebih dahulu!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Kedisiplinan Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-radial-dots { 
            background-color: #0f172a; 
            background-image: radial-gradient(#334155 1px, transparent 1px); 
            background-size: 24px 24px; 
        }
        .glass-card { 
            background: rgba(30, 41, 59, 0.85); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
    </style>
</head>
<body class="bg-radial-dots text-slate-200 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="glass-card max-w-md w-full p-8 rounded-2xl shadow-2xl border border-slate-700/60">
        <!-- Header Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Sistem Kedisiplinan</h1>
            <p class="text-xs text-slate-400 mt-1">SMK Muhammadiyah Kota Tasikmalaya</p>
        </div>

        <!-- Alert Error -->
        <?php if (!empty($error)): ?>
            <div class="bg-rose-950/80 border border-rose-800 text-rose-300 p-3.5 rounded-xl text-sm mb-6 flex items-center gap-3 shadow-md">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form method="POST" action="login.php" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold tracking-wider text-slate-400 uppercase mb-2">Email</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                    <input type="email" name="email" required placeholder="admin@sekolah.sch.id" class="w-full pl-11 pr-4 py-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold tracking-wider text-slate-400 uppercase mb-2">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-500 text-sm"></i>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full pl-11 pr-4 py-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-900/50 transition-all duration-200 mt-2 flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk Sistem
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-700/60 text-center">
            <p class="text-xs text-slate-500">© 2026 UKK Rekayasa Perangkat Lunak</p>
        </div>
    </div>

</body>
</html>