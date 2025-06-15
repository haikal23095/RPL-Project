<?php
session_start();
require "db.php"; // Pastikan db.php ada dan berisi koneksi $kon
require_once './vendor/autoload.php'; // pastikan path composer autoload benar


use Dotenv\Dotenv;
use Twilio\Rest\Client;


// $dotenv = Dotenv::createImmutable(__DIR__);
// $dotenv->load();


$sid = 'AC4fcb38388f5c58c449b150823cf9b4eb';
$token = '43275be86fd2e9cd95758de41186f0de';
$verifySid = 'VA1c3e751033905756f2848f4aeb7f4b0c';


// Debug sementara
// echo "SID: " . ($sid ?? 'NULL') . "<br>";
// echo "TOKEN: " . ($token ?? 'NULL') . "<br>";
// echo "VERIFY SID: " . ($verifySid ?? 'NULL') . "<br>";

$msg = ""; // Variabel untuk pesan sukses/error

$login_berhasil = false;
// Fungsi untuk membersihkan input
function sanitize_input($kon, $data) {
    return mysqli_real_escape_string($kon, trim($data)); // Tambahkan trim() untuk menghapus spasi di awal/akhir
}

// Logika untuk proses LOGIN
if (isset($_POST["login"])) {
    $email = sanitize_input($kon, $_POST["email"] ?? "");
    $pwd = sanitize_input($kon, $_POST["pwd"] ?? "");

    if (empty($email) || empty($pwd)) {
        $msg = '<div class="alert alert-warning">&nbsp; MAAF, EMAIL / PASSWORD ANDA MASIH KOSONG. SILAHKAN ISI DENGAN BENAR!</div>';
    } else {
        // Periksa user di database
        // **KEAMANAN: Gunakan password_verify() jika password di-hash di DB**
        $query_user = mysqli_query($kon, "SELECT * FROM user WHERE email = '$email'");
        $user_data = mysqli_fetch_array($query_user);

        if (!$user_data || $user_data['password'] !== $pwd) { // Cek password plain text (GANTI DENGAN password_verify($pwd, $user_data['password_hash']))
            $msg = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>&nbsp; MAAF, EMAIL / PASSWORD ANDA SALAH. SILAHKAN ULANGI LAGI!</div>';
        } else {
            // simpan session
            $_SESSION['user'] = $user_data;
            // Update active field
            $update_active = "UPDATE user SET active = NOW() WHERE email = '$email'";
            mysqli_query($kon, $update_active);

            
            $login_berhasil = true;
            $user = $user_data; // Simpan user ke variabel session atau variabel lokal
            
            $nomor = $user_data['no_tlp'];
            if (strpos($nomor, '0') === 0) {
                $nomor = '+62' . substr($nomor, 1);
            }
            
            // Kirim OTP via Twilio Verify
            try {
                $twilio = new Client($sid, $token);
                $verification = $twilio->verify->v2->services($verifySid)
                    ->verifications
                    ->create($nomor, "sms");
                
                
                // Redirect ke halaman verifikasi jika OTP berhasil dikirim
                header("Location: verification.php");
                exit();
            } catch (Exception $e) {
                // print_r($_SESSION);
                $msg = '<div class="alert alert-danger">Gagal mengirim OTP: ' . $e->getMessage() . '</div>';
                // exit();
            }
        }

            
    }
}



// Logika untuk proses REGISTER
if (isset($_POST["register"])) {
    $nama_user = sanitize_input($kon, $_POST["nama_user"] ?? "");
    $email = sanitize_input($kon, $_POST["email"] ?? "");
    $password = sanitize_input($kon, $_POST["password"] ?? "");
    $no_tlp = sanitize_input($kon, $_POST["no_tlp"] ?? "");
    $alamat = sanitize_input($kon, $_POST["alamat"] ?? "");
    $level = "user"; // Level untuk registrasi baru selalu 'user'

    // Validasi field yang kosong (sesuai yang terlihat di gambar UI register)
    if (empty($nama_user) || empty($email) || empty($password)) { // no_tlp dan alamat tidak wajib sesuai gambar UI
        $msg = '<div class="alert alert-warning">&nbsp; MAAF, NAMA, EMAIL & PASSWORD HARUS DIISI. SILAHKAN ISI DENGAN BENAR!</div>';
    } else {
        // Cek apakah email sudah terdaftar
        $check_email_query = mysqli_query($kon, "SELECT * FROM user WHERE email = '$email'");
        if (mysqli_num_rows($check_email_query) > 0) {
            $msg = '<div class="alert alert-danger">&nbsp; MAAF, EMAIL SUDAH TERDAFTAR. SILAKAN GUNAKAN EMAIL LAIN!</div>';
        } else {
            // **KEAMANAN: Hash password sebelum disimpan!**
            // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Ganti $password dengan $hashed_password di query INSERT

            $insert_query = "INSERT INTO user (nama, email, password, no_tlp, alamat, level) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($kon, $insert_query);
            mysqli_stmt_bind_param($stmt, "ssssss", $nama_user, $email, $password, $no_tlp, $alamat, $level); // GANTI $password jika pakai hash
            
            if (mysqli_stmt_execute($stmt)) {
                // Redirect ke halaman login dengan query parameter untuk menampilkan panel register
                header("Location: login.php?show_register=true&reg_success=true");
                exit();
            } else {
                $msg = '<div class="alert alert-danger">&nbsp; REGISTER GAGAL. SILAHKAN ULANGI LAGI! Error: ' . mysqli_error($kon) . '</div>';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Tampilkan pesan sukses reset password jika ada
if (isset($_SESSION['reset_success'])) {
    $msg = '<div class="alert alert-success">' . $_SESSION['reset_success'] . '</div>';
    unset($_SESSION['reset_success']); // Hapus dari session agar tidak tampil lagi
}
?>



<!DOCTYPE html>
<html>
<head>
	<title>CasaLuxe Login Page</title>
	<link rel="stylesheet" type="text/css" href="assets/css/login.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
  <link href="https://code.iconify.design/3/3.1.0/iconify.min.css" rel="stylesheet">
  <!-- Favicons -->
  <link href="assets/img/LOGOCASALUXE2.png" rel="icon" sizes="48x48">
</head>
<body>
  <header>
            <a href="landing_page.php">
                <img src="assets/img/logo_CasaLuxe.png" alt="Logo" class="logo">
            </a>
    </header>
	<div class="container" id="main">
      <div class="sign-up">
        <?php if (!empty($msg) && isset($_POST['register'])) echo $msg; ?>
        <form class="register" method="post">
            <h1>Daftar</h1>
            <div class="social-container">
                <a href="#" class="social"><span class="grommet-icons--facebook-option"></span></a>
                <a href="#" class="social"><span class="flat-color-icons--google"></span></a>
            </div>
            <p>atau buat akun baru</p>
            <input type="text" name="nama_user" placeholder="Nama Lengkap" required="">
            <input type="email" name="email" placeholder="Email" required="">
            <input type="text" name="no_tlp" placeholder="Nomor Telepon" required="">
            <input type="text" name="alamat" placeholder="Alamat" required="">
            <input type="password" name="password" placeholder="Kata Sandi" required="">
            <select name="level" class="form-control">
                <option value="">Pilih Level Anda Sebagai User</option>
                <option value="user">User</option>
            </select>            
            <button name="register" type="submit">Daftar</button>
        </form>
      </div>

      <div class="sign-in">
        <?php if (!empty($msg) && isset($_POST['login'])) echo $msg; ?>
          <form class="login" method="post">
              <h1>Masuk</h1>
              <div class="social-container">
                  <a href="#" class="social"><span class="grommet-icons--facebook-option"></span></a>
                  <a href="#" class="social"><span class="flat-color-icons--google"></span></a>
              </div>
              <p>atau gunakan akunmu</p>
              <input type="email" name="email" placeholder="Email" required="">
              <input type="password" name="pwd" placeholder="Kata Sandi" required="">
              <a href="forgot_password.php">Lupa kata sandimu?</a>
              <button name="login" type="submit">Masuk</button>
          </form>
      </div>

      <div class="overlay-container">
        <div class="overlay">
          <div class="overlay-left">
            <h1>Selamat Datang Kembali!</h1>
            <p>Untuk tetap terhubung dengan kami, silakan masuk dengan akun pribadi Anda</p>
            <button id="signIn">Masuk</button>
          </div>
          <div class="overlay-right">
            <h1>Hi, SoCa!</h1>
            <p>Silakan masukkan data pribadi Anda dan mulailah merasakan kegembiraan bersama kami.</p>
            <button id="signUp">Daftar</button>
          </div>
        </div>
      </div>
    </div>
    <script src="assets/js/login.js" type="text/javascript"></script>
</body>
</html>