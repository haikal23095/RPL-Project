<?php
session_start();

require_once './vendor/autoload.php';
use Twilio\Rest\Client;

$sid    = 'AC4fcb38388f5c58c449b150823cf9b4eb';
$token  = 'cced338e264b32598d3adca6acdb624b';
$verifySid = 'VA1c3e751033905756f2848f4aeb7f4b0c';
$twilio = new Client($sid, $token);

// Redirect jika belum login (tidak ada data user)
if (!isset($_SESSION['user']['no_tlp'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user']['email'] ?? 'your@email.com';


?>
<!-- Form HTML tetap, tambahkan handler error jika perlu -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .verification-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .verification-text {
            text-align: center;
            margin-bottom: 30px;
            color: #6c757d;
        }
        .btn-verify {
            width: 100%;
            padding: 10px;
            background-color: orange;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .resend-code {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            cursor: pointer;
        }
        .resend-code:hover {
            color: #0d6efd;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-title">Verification Code</div>
        <div class="verification-text">
            We sent it to <?= htmlspecialchars($_SESSION['user']['email'] ?? 'your@email.com') ?>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="verify.php" id="verificationForm">
            <div class="d-flex justify-content-center mb-4">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" class="form-control verification-input mx-1 text-center"
                           maxlength="1"
                           style="width: 50px; height: 50px; font-size: 20px;">
                <?php endfor; ?>
            </div>

            <!-- Hidden input to hold the full OTP code -->
            <input type="hidden" name="otp" id="otp">

            <button type="submit" class="btn btn-verify mb-3" id="verifyBtn">Verify Code</button>
        </form>

        <div class="resend-code" id="resendCode" style="cursor:pointer; color:blue;">Resend code</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.verification-input');
            const hiddenOtp = document.getElementById('otp');
            const form = document.getElementById('verificationForm');

            // Auto-focus ke input pertama
            inputs[0].focus();

            // Pindah focus antar input
            inputs.forEach((input, index) => {
                input.addEventListener('input', function () {
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            // Gabungkan 6 digit ke input hidden sebelum submit
            form.addEventListener('submit', function (e) {
                let code = '';
                inputs.forEach(input => code += input.value);

                if (code.length === 6) {
                    hiddenOtp.value = code;
                    // Form akan submit normal
                } else {
                    e.preventDefault(); // cegah submit
                    alert('Please enter the complete 6-digit code');
                }
            });

            // Handle resend code
            document.getElementById('resendCode').addEventListener('click', function () {
                window.location.href = 'resend.php';
            });
        });
    </script>
</body>

</html>