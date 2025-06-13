<?php
session_start();
require "db.php"; // Pastikan db.php ada dan berisi koneksi $kon

$msg = ""; // Untuk pesan sukses/error

// Fungsi untuk membersihkan input
function sanitize_input($kon, $data) {
    return mysqli_real_escape_string($kon, trim($data));
}

if (isset($_POST["submit_email"])) {
    $email = sanitize_input($kon, $_POST["email"] ?? "");

    if (empty($email)) {
        $msg = '<div class="alert alert-warning">Mohon masukkan alamat email Anda.</div>';
    } else {
        // 1. Cek apakah email terdaftar di database
        $check_email_query = mysqli_query($kon, "SELECT * FROM user WHERE email = '$email'");
        if (mysqli_num_rows($check_email_query) == 0) {
            // Penting: Untuk keamanan, jangan beritahu apakah email terdaftar atau tidak.
            // Selalu tampilkan pesan sukses yang sama, meskipun email tidak ditemukan.
            // Ini mencegah enumerasi email.
            $msg = '<div class="alert alert-success">Jika email Anda terdaftar, link reset kata sandi telah dikirim ke email Anda. Silakan cek kotak masuk Anda.</div>';
        } else {
            // 2. Hasilkan token unik dan aman
            // random_bytes() lebih kuat dari rand()
            $token = bin2hex(random_bytes(32)); // Token acak 64 karakter heksadesimal
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token berlaku 1 jam

            // 3. Simpan token ke tabel password_resets
            // Hapus token lama yang mungkin ada untuk email ini
            mysqli_query($kon, "DELETE FROM password_resets WHERE email = '$email'");
            
            // Masukkan token baru
            $insert_token_query = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($kon, $insert_token_query);
            mysqli_stmt_bind_param($stmt, "sss", $email, $token, $expires_at);
            
            if (mysqli_stmt_execute($stmt)) {
                // 4. Kirim email berisi link reset kata sandi
                // GANTI DENGAN URL ASLI DOMAIN ANDA UNTUK PRODUKSI
                // Contoh: $reset_link = "https://www.casaluxe.com/reset_password.php?token=" . $token;
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token; // Menggunakan host saat ini

                $subject = "Reset Kata Sandi Anda - CasaLuxe";
                $headers = "From: no-reply@casaluxe.com\r\n";
                $headers .= "Reply-To: no-reply@casaluxe.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                $message = "Halo,\n\n"
                         . "Anda menerima email ini karena ada permintaan reset kata sandi untuk akun Anda.\n"
                         . "Silakan klik link berikut untuk mereset kata sandi Anda:\n\n"
                         . $reset_link . "\n\n"
                         . "Link ini akan kadaluarsa dalam 1 jam.\n"
                         . "Jika Anda tidak meminta reset kata sandi, abaikan email ini.\n\n"
                         . "Terima kasih,\nTim CasaLuxe";

                // --- PENTING: Konfigurasi Pengiriman Email ---
                // Fungsi mail() PHP membutuhkan konfigurasi server (misal: sendmail, Postfix di Linux, atau SMTP server)
                // DI LINGKUNGAN LOKAL (XAMPP/WAMPServer), Anda mungkin perlu mengkonfigurasi php.ini atau menggunakan library SMTP (PHPMailer).
                // Jika tidak terkonfigurasi, fungsi mail() akan gagal diam-diam atau pesan tidak terkirim.
                // Untuk produksi, SANGAT disarankan menggunakan library seperti PHPMailer atau layanan email API (SendGrid, Mailgun, dll.)
                
                // mail($email, $subject, $message, $headers); // Uncomment ini setelah konfigurasi email

                $msg = '<div class="alert alert-success">Link reset kata sandi telah dikirim ke email Anda. Silakan cek kotak masuk Anda.</div>';

            } else {
                $msg = '<div class="alert alert-danger">Terjadi kesalahan saat membuat token reset. Mohon coba lagi. Error: ' . mysqli_error($kon) . '</div>';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lupa Kata Sandi CasaLuxe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/login.css"> <!-- Re-use login.css for styling -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="assets/img/LOGOCASALUXE2.png" rel="icon" sizes="48x48">
</head>
<body>
    <!-- Logo GALeri -->
    <header>
            <a href="#">
                <img src="assets/img/logo_CasaLuxe.png" alt="Logo" class="logo">
            </a>
    </header>

    <div class="container" id="forgot-password-main">
        <div class="form-container">
            <form method="post" action="forgot_password.php">
                <h1>Lupa Kata Sandi?</h1>
                <p>Masukkan alamat email Anda yang terdaftar dan kami akan mengirimkan link untuk mereset kata sandi Anda.</p>
                <?php if (!empty($msg)) echo $msg; ?>
                <input type="email" name="email" placeholder="Alamat Email" required="">
                <button type="submit" name="submit_email">Kirim Link Reset</button>
                <a href="login.php" style="margin-top: 20px;">Kembali ke Halaman Masuk</a>
            </form>
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
        #forgot-password-main {
            width: 500px; /* Sesuaikan lebar container */
            max-width: 90%;
            min-height: 350px; /* Sesuaikan tinggi container */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #forgot-password-main .form-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #forgot-password-main form {
            background: #ffffff;
            border-radius: 10px;
            padding: 40px; /* Sesuaikan padding */
            box-shadow: none; /* Hapus shadow tambahan jika ada dari login.css */
        }
        #forgot-password-main h1 {
            font-size: 30px; /* Sesuaikan ukuran font */
        }
        #forgot-password-main p {
            margin-bottom: 30px;
        }
        /* Override alert styles from login.css if needed */
        .alert {
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
        }
        @media (max-width: 768px) {
            #forgot-password-main {
                min-height: 300px;
            }
            #forgot-password-main form {
                padding: 30px 20px;
            }
            #forgot-password-main h1 {
                font-size: 24px;
            }
            .header-logo {
                font-size: 24px;
                top: 10px;
                left: 10px;
            }
        }
    </style>
</body>
</html>