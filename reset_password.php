<?php
session_start();
require "db.php"; // Pastikan db.php ada dan berisi koneksi $kon

$msg = "";
$token_valid = false;
$token = $_GET['token'] ?? ''; // Ambil token dari URL

// Fungsi untuk membersihkan input
function sanitize_input($kon, $data) {
    return mysqli_real_escape_string($kon, trim($data));
}

if (!empty($token)) {
    // 1. Cek token di database: apakah ada dan belum kadaluarsa
    $check_token_query = mysqli_query($kon, "SELECT * FROM password_resets WHERE token = '$token' AND expires_at > NOW()");
    $token_data = mysqli_fetch_array($check_token_query);

    if ($token_data) {
        $token_valid = true; // Token valid dan belum kadaluarsa
        $user_email = $token_data['email']; // Simpan email pengguna terkait token ini
    } else {
        $msg = '<div class="alert alert-danger">Link reset kata sandi tidak valid atau sudah kadaluarsa.</div>';
    }
} else {
    $msg = '<div class="alert alert-danger">Token reset kata sandi tidak ditemukan di URL.</div>';
}

// Logika untuk proses reset kata sandi baru
if ($token_valid && isset($_POST["reset_password"])) {
    $new_password = sanitize_input($kon, $_POST["new_password"] ?? "");
    $confirm_password = sanitize_input($kon, $_POST["confirm_password"] ?? "");

    if (empty($new_password) || empty($confirm_password)) {
        $msg = '<div class="alert alert-warning">Mohon masukkan kata sandi baru dan konfirmasinya.</div>';
    } elseif ($new_password !== $confirm_password) {
        $msg = '<div class="alert alert-danger">Konfirmasi kata sandi tidak cocok.</div>';
    } elseif (strlen($new_password) < 6) { // Contoh: Minimal 6 karakter
        $msg = '<div class="alert alert-warning">Kata sandi minimal 6 karakter.</div>';
    } else {
        // --- SANGAT PENTING: HASH KATA SANDI BARU ---
        // Anda harus mengubah kolom 'password' di tabel 'user' menjadi lebih panjang (misalnya VARCHAR(255))
        // untuk menyimpan hash.
        // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        // Ganti $hashed_password_for_demo dengan $hashed_password di query UPDATE

        // Untuk demo ini, kita masih menggunakan plain text (sesuaikan dengan db.php Anda)
        $hashed_password_for_demo = $new_password; 

        // 2. Update kata sandi pengguna
        // Gunakan email yang terkait dengan token, BUKAN dari input form (demi keamanan)
        $update_pwd_query = "UPDATE user SET password = '$hashed_password_for_demo' WHERE email = '$user_email'";
        
        if (mysqli_query($kon, $update_pwd_query)) {
            // 3. Hapus token dari database (agar tidak bisa digunakan lagi)
            mysqli_query($kon, "DELETE FROM password_resets WHERE token = '$token'");

            // Redirect ke halaman login dengan pesan sukses
            $_SESSION['reset_success'] = "Kata sandi Anda berhasil direset. Silakan masuk dengan kata sandi baru Anda.";
            header("Location: login.php");
            exit;
        } else {
            $msg = '<div class="alert alert-danger">Terjadi kesalahan saat mereset kata sandi Anda. Error: ' . mysqli_error($kon) . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Kata Sandi - CasaLuxe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/login.css"> <!-- Re-use login.css for styling -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="assets/img/LOGOCASALUXE2.png" rel="icon" sizes="48x48">
</head>
<body>
    <a href="#">
                <img src="assets/img/logo_CasaLuxe.png" alt="Logo" class="logo">
            </a>

    <div class="container" id="reset-password-main">
        <div class="form-container">
            <form method="post" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
                <h1>Reset Kata Sandi Anda</h1>
                <?php if (!empty($msg)) echo $msg; ?>

                <?php if ($token_valid): ?>
                    <p>Masukkan kata sandi baru Anda.</p>
                    <input type="password" name="new_password" placeholder="Kata Sandi Baru" required="">
                    <input type="password" name="confirm_password" placeholder="Konfirmasi Kata Sandi Baru" required="">
                    <button type="submit" name="reset_password">Reset Kata Sandi</button>
                <?php else: ?>
                    <p>Link reset tidak valid atau sudah kadaluarsa. Mohon kembali ke halaman 'Lupa Kata Sandi?' untuk mendapatkan link baru.</p>
                    <a href="forgot_password.php" style="margin-top: 20px;">Dapatkan Link Baru</a>
                <?php endif; ?>
                <a href="login.php" style="margin-top: 20px;">Kembali ke Halaman Masuk</a>
            </form>
        </div>
    </div>
</body>
</html>