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
    <style>
        /* Impor Font dari Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');

        /* Reset CSS */
        *{
            box-sizing: border-box;
        }
        body{
            font-family: 'Andika', sans-serif;
            width: 100vw; /* Mengisi lebar penuh viewport */
            height: 100vh; /* Mengisi tinggi penuh viewport */
            display: flex;
            justify-content: center; /* Pusatkan container secara horizontal */
            align-items: center;   /* Pusatkan container secara vertikal */
            flex-direction: column; /* Tata elemen secara kolom (opsional, jika ada footer dsb) */
            background: #f0f2f5; /* Warna latar belakang sesuai gambar */
            margin: 0; /* Menghilangkan margin default yang bisa menyebabkan scroll */
            overflow: hidden; /* **PENTING: Menghilangkan scrollbar pada body** */
            position: relative; /* Penting untuk positioning absolute header */
        }

        /* Header logo */
        header {
            position: absolute; /* **Memposisikan header secara absolut** */
            top: 30px; /* Jarak dari atas */
            left: 30px; /* Jarak dari kiri */
            width: auto; /* Biarkan lebar ditentukan oleh kontennya */
            z-index: 10; /* Pastikan header berada di atas elemen lain */
        }

        header .logo {
            width: 100px;
            height: auto;
            margin: 0;
            padding: 0;
        }

        /* Container utama untuk form reset password */
        .container { /* Menggunakan .container yang sama dengan login.php untuk konsistensi */
            position: relative;
            width: 500px; /* Sesuaikan lebar container reset password */
            max-width: 90%;
            height: 380px; /* **Menentukan tinggi tetap untuk container** */
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden; /* Sembunyikan overflow di dalam container */
            box-shadow: 0 14px 28px rgba(0,0,0,0.25),
                        0 10px 10px rgba(0,0,0,0.22);
            z-index: 1;
            /* Flexbox untuk centering konten di dalam container */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Form container (langsung ke form, karena tidak ada overlay) */
        .form-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px 40px; /* Sesuaikan padding form untuk kerapatan */
            height: 100%; /* Form mengisi tinggi parent */
            border-radius: 10px; /* Border radius form */
            text-align: center;
            box-shadow: none; /* Hapus shadow tambahan jika ada dari login.css */
            overflow-y: auto; /* Izinkan scroll hanya pada form jika konten meluap */
        }

        h1{
            font-weight: bold;
            margin: 0;
            font-size: 26px; /* Ukuran font H1 sedikit lebih besar dari di login.php */
            margin-bottom: 15px; /* Jarak bawah dikurangi */
            font-family: "Aclonica", sans-serif;
            letter-spacing: 2px;
            text-shadow: 0px 0px 4px rgba(0,0,0,0.26);
        }
        p{
            font-size: 13px; /* Ukuran font P sedikit lebih besar */
            font-weight: 100;
            line-height: 1.5;
            letter-spacing: 0.5px;
            margin: 5px 0 20px; /* Jarak diubah */
            font-family: "Andika", sans-serif;
        }
        input{
            background: #eee;
            padding: 10px 15px; /* Padding input */
            margin: 8px 0; /* Margin input */
            width: 100%;
            height: auto;
            min-height: 40px; /* Min-height untuk input */
            border-radius: 5px;
            border: none;
            outline: none;
        }
        a{
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0; /* Jarak link */
        }
        button{
            color: #fff;
            background: #ff4b2b;
            font-size: 12px;
            font-weight: bold;
            font-family: 'Andika', sans-serif;
            padding: 12px 35px;
            margin: 10px;
            border-radius: 19px;
            border: 1px solid #ff4b2b;
            outline: none;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }
        button:active{
            transform: scale(0.90);
        }
        button:hover{
            box-shadow: 0 0 10px #f05c34,
            0 0 10px #f05c34;
        }

        /* Style untuk pesan alert */
        .alert {
            width: 100%;
            margin-bottom: 15px; /* Jarak bawah pesan */
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            box-sizing: border-box;
        }
        .alert-warning {
            background-color: #ffeeba;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f8d7da;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #d4edda;
        }

        /* Media Queries untuk Responsif */
        @media (max-width: 768px) {
            body {
                padding: 10px; /* Kurangi padding body di mobile */
            }
            header {
                top: 10px;
                left: 10px;
            }
            header .logo {
                width: 80px; /* Logo lebih kecil di mobile */
            }
            .container {
                width: 95%; /* Lebih lebar di mobile */
                height: auto; /* Auto height untuk form yang lebih panjang */
                min-height: 400px; /* Minimal tinggi */
            }
            form {
                padding: 20px; /* Padding form lebih kecil */
            }
            h1 {
                font-size: 22px;
                margin-bottom: 10px;
            }
            p {
                font-size: 12px;
                margin: 5px 0 15px;
            }
            input {
                margin: 6px 0;
                min-height: 35px;
            }
            button {
                padding: 10px 25px;
                margin: 8px;
            }
        }
    </style>
    <header>
            <a href="#">
                <img src="assets/img/logo_CasaLuxe.png" alt="Logo" class="logo">
            </a>
    </header>   

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