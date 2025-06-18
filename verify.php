<?php
session_start();
require_once './vendor/autoload.php';

use Twilio\Rest\Client;

// 1. Validasi sesi terlebih dahulu

if (empty($_SESSION['user']['no_tlp']) || empty($_SESSION['user']['level'])) {
    header("Location: login.php"); // atau halaman login kamu
    exit();
}

$userPhone = $_SESSION['user']['no_tlp'];
$user_data = $_SESSION['user'];



// 2. Setup Twilio client (lebih baik ambil dari config)
$sid = 'ACe52064ce7d7f46c9b2c1bdc15492c17a';
$token = '686264dd59f4d3625b1bfc474426b25d';
$verifySid = 'VA94b25bc03e55d9e18ec610f38d09a4e0';

$twilio = new Client($sid, $token);

// 3. Handle POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST["otp"] ?? '';

    if (empty($code)) {
        $_SESSION['error'] = "Kode OTP tidak boleh kosong.";
        header("Location: verification.php");
        exit();
    }

    try {
        $verificationCheck = $twilio->verify->v2->services($verifySid)
            ->verificationChecks
            ->create([
                "to" => $userPhone,
                "code" => $code
            ]);

        if ($verificationCheck->status === "approved") {
            $_SESSION['otp_verified'] = true;

            // Set session and redirect based on level
            switch ($user_data["level"]) {
                case "admin":
                    $_SESSION["admin"] = $user_data["nama"];
                    header("Location: admin/index.php");
                    exit;
                case "cs":
                    $_SESSION["cs"] = $user_data["nama"];
                    header("Location: cs/chat.php");
                    exit;
                case "user":
                    $_SESSION["user"] = $user_data["nama"];
                    header("Location: user/index.php");
                    exit;
                default:
                    $msg = '<div class="alert alert-danger">&nbsp; ERROR: Level pengguna tidak dikenal.</div>';
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "Kode OTP salah atau sudah kedaluwarsa.";
            header("Location: verification.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan saat verifikasi: " . $e->getMessage();
        header("Location: verification.php");
        exit();
    }
} else {
    // Jika bukan POST
    header("Location: verification.php");
    exit();
}
