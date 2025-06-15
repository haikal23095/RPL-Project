<?php
session_start();
require "db.php"; // Pastikan db.php ada dan berisi koneksi $kon

$msg = "";
$token_valid = false;
$email_from_token = ""; // Akan diisi dengan email dari token yang valid

// Fungsi untuk membersihkan input
function sanitize_input($kon, $data) {
    return mysqli_real_escape_string($kon, trim($data));
}

// 1. Logika untuk memvalidasi token dari URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = sanitize_input($kon, $_GET['token']);

    // Cek token di database, pastikan belum kadaluarsa
    $query_token = mysqli_query($kon, "SELECT * FROM password_resets WHERE token = '$token' AND expires_at > NOW()");
    $token_data = mysqli_fetch_array($query_token);

    if ($token_data) {
        $token_valid = true;
        $email_from_token = $token_data['email']; // Simpan email dari token yang valid
    } else {
        $msg = '<div class="alert alert-danger">Link reset kata sandi tidak valid atau sudah kadaluarsa.</div>';
    }
} else if (isset($_POST['reset_password'])) {
    // 2. Logika saat form reset password disubmit
    $token = sanitize_input($kon, $_POST['token'] ?? "");
    $new_password = sanitize_input($kon, $_POST['new_password'] ?? "");
    $confirm_password = sanitize_input($kon, $_POST['confirm_password'] ?? "");

    // Verifikasi ulang token (penting untuk keamanan)
    $query_token = mysqli_query($kon, "SELECT * FROM password_resets WHERE token = '$token' AND expires_at > NOW()");
    $token_data = mysqli_fetch_array($query_token);

    if (!$token_data) {
        $msg = '<div class="alert alert-danger">Link reset kata sandi tidak valid atau sudah kadaluarsa. Silakan coba lagi.</div>';
    } else if (empty($new_password) || empty($confirm_password)) {
        $msg = '<div class="alert alert-warning">Kata sandi baru dan konfirmasi kata sandi harus diisi.</div>';
    } else if ($new_password !== $confirm_password) {
        $msg = '<div class="alert alert-danger">Kata sandi baru dan konfirmasi kata sandi tidak cocok.</div>';
    } else if (strlen($new_password) < 6) { // Contoh: minimal 6 karakter
        $msg = '<div class="alert alert-warning">Kata sandi minimal 6 karakter.</div>';
    } else {
        $email_to_reset = $token_data['email']; // Ambil email dari token yang valid

        // --- KEAMANAN KRITIS: Hash password sebelum disimpan! ---
        // Anda harus mengubah kolom 'password' di tabel 'user' menjadi VARCHAR(255)
        // untuk menyimpan hash.
        $hashed_new_password = ($new_password);
        // GANTI INI: $hashed_new_password = $new_password; // Ini hanya untuk demo dengan password plain text

        // Update password di tabel user
        // PERHATIAN: Asumsi kolom password Anda bernama 'password'. Ganti jika berbeda.
        $update_password_query = "UPDATE user SET password = ? WHERE email = ?";
        $stmt = mysqli_prepare($kon, $update_password_query);
        mysqli_stmt_bind_param($stmt, "ss", $hashed_new_password, $email_to_reset);

        if (mysqli_stmt_execute($stmt)) {
            // Hapus token dari tabel password_resets setelah berhasil digunakan
            mysqli_query($kon, "DELETE FROM password_resets WHERE email = '$email_to_reset'");

            $_SESSION['reset_success'] = "Kata sandi Anda berhasil direset. Silakan masuk dengan kata sandi baru Anda.";
            header("Location: login.php");
            exit();
        } else {
            $msg = '<div class="alert alert-danger">Gagal mereset kata sandi. Silakan coba lagi. Error: ' . mysqli_error($kon) . '</div>';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Jika tidak ada token di URL saat pertama kali diakses, atau bukan POST request
    $msg = '<div class="alert alert-warning">Token reset kata sandi tidak ditemukan atau tidak valid.</div>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Kata Sandi CasaLuxe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/login.css">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="assets/img/LOGOCASALUXE2.png" rel="icon" sizes="48x48">
</head>
<body>
    <header>
            <a href="#">
                <img src="assets/img/logo_CasaLuxe.png" alt="Logo" class="logo">
            </a>
    </header>

    <div class="container" id="reset-password-main">
        <div class="form-container">
            <?php if (!empty($msg)) echo $msg; ?>
            <?php if ($token_valid): // Tampilkan form jika token valid ?>
                <form method="post" action="reset_password.php">
                    <h1>Reset Kata Sandi</h1>
                    <p>Masukkan kata sandi baru Anda.</p>
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="password" name="new_password" placeholder="Kata Sandi Baru" required="">
                    <input type="password" name="confirm_password" placeholder="Konfirmasi Kata Sandi Baru" required="">
                    <button type="submit" name="reset_password">Reset Kata Sandi</button>
                    <a href="login.php" style="margin-top: 20px;">Kembali ke Halaman Masuk</a>
                </form>
            <?php else: // Tampilkan pesan error jika token tidak valid ?>
                <p>Silakan kembali ke halaman lupa kata sandi untuk meminta link baru.</p>
                <a href="forgot_password.php">Lupa Kata Sandi?</a>
            <?php endif; ?>
        </div>
    </div>

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        #reset-password-main {
            width: 500px; /* Sesuaikan lebar container */
            max-width: 90%;
            min-height: 400px; /* Sesuaikan tinggi container */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #reset-password-main .form-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #reset-password-main form {
            background: #ffffff;
            border-radius: 10px;
            padding: 40px; /* Sesuaikan padding */
            box-shadow: none; /* Hapus shadow tambahan jika ada dari login.css */
        }
        #reset-password-main h1 {
            font-size: 30px; /* Sesuaikan ukuran font */
        }
        #reset-password-main p {
            margin-bottom: 30px;
        }
        /* Override alert styles from login.css if needed */
        .alert {
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }
        @media (max-width: 768px) {
            #reset-password-main {
                min-height: 350px;
            }
            #reset-password-main form {
                padding: 30px 20px;
            }
            #reset-password-main h1 {
                font-size: 24px;
            }
        }
    </style>
</body>
</html>