<?php
session_start();
include '../db.php';
$page = "chat";

// Pastikan user telah login
if (!isset($_SESSION['user'])) {
    die("Harap login terlebih dahulu.");
}

$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);

$user_id = $row_user['id_user'];
$user_photo = $row_user['foto_profil'] ?? 'default.png';

// Ambil data admin
$admin_query = "SELECT nama, foto FROM user WHERE level='cs' LIMIT 1";
$admin_result = mysqli_query($kon, $admin_query);
$admin_data = mysqli_fetch_assoc($admin_result);
$admin_name = $admin_data['nama'] ?? 'Admin';
$admin_photo = $admin_data['foto'] ?? 'default.png';

// Logika Post-Redirect-Get untuk mengirim pesan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty(trim($_POST['message']))) {
    $message = mysqli_real_escape_string($kon, trim($_POST['message']));
    
    $query = "INSERT INTO messages (sender, user_id, message) VALUES ('user', '$user_id', '$message')";
    
    if (mysqli_query($kon, $query)) {
        header("Location: chat.php");
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($kon);
    }
}

// Ambil riwayat pesan
$query = "SELECT * FROM messages WHERE user_id='$user_id' ORDER BY timestamp ASC";
$result = mysqli_query($kon, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Customer Service</title>
    
    <?php include 'aset.php'; ?>

    <style>
        .chat-card {
            display: flex;
            flex-direction: column;
            height: 75vh;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            background-color: #fff;
        }
        .chat-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
        }
        .chat-body {
            flex-grow: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background-color: #f8f9fa;
        }
        .chat-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
        }
        .message-row {
            display: flex;
            margin-bottom: 1.25rem;
        }

        /* --- PERBAIKAN DI SINI --- */
        .message-bubble {
            display: inline-block; 
            padding: 0.7rem 1.1rem;
            border-radius: 1.25rem;
            max-width: 75%;
            line-height: 1.4;
            word-wrap: break-word;
            min-width: 80px;      
            text-align: left;     
        }

        .message-row.sent {
            justify-content: flex-end;
        }
        .message-row.sent .message-bubble {
            background-color: #0d6efd;
            color: white;
            border-top-right-radius: 0.5rem;
        }
        .message-row.received {
            justify-content: flex-start;
        }
        .message-row.received .message-bubble {
            background-color: #e9ecef;
            color: #212529;
            border-top-left-radius: 0.5rem;
        }
        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 4px;
            padding: 0 0.5rem;
        }
        .message-row.sent .message-time {
            text-align: right;
        }
        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }
        .chat-footer .form-control {
            border-radius: 1.5rem;
            border-color: #ced4da;
        }
        .chat-footer .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        }
        .chat-footer .btn-send {
            border-radius: 50%;
            width: 45px;
            height: 45px;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-headset"></i> Pelayanan Pelanggan</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Chat</li>
                </ol>
            </nav>
        </div>
        
        <section class="section">
            <div class="chat-card">
                <div class="chat-header d-flex align-items-center">
                    <img src="../uploads/<?= htmlspecialchars($admin_photo); ?>" alt="Admin" class="profile-pic me-3">
                    <div>
                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($admin_name); ?></h6>
                        <small class="text-success"><i class="bi bi-circle-fill" style="font-size: 0.6rem;"></i> Online</small>
                    </div>
                </div>

                <div class="chat-body" id="chat-box">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="message-row <?php echo $row['sender'] == 'user' ? 'sent' : 'received'; ?>">
                            <div class="d-flex flex-column <?php echo $row['sender'] == 'user' ? 'align-items-end' : 'align-items-start'; ?>">
                                <div class="message-bubble">
                                    <?= nl2br(htmlspecialchars($row['message'])); ?>
                                </div>
                                <div class="message-time">
                                    <?= date('H:i', strtotime($row['timestamp'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="chat-footer">
                    <form method="POST" action="chat.php" class="d-flex align-items-center">
                        <input type="text" name="message" class="form-control" placeholder="Ketik pesan Anda..." required autocomplete="off">
                        <button class="btn btn-primary btn-send ms-2 flex-shrink-0" type="submit">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.getElementById('chat-box');
            // Scroll ke pesan paling bawah saat halaman dimuat
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>
</body>
</html>