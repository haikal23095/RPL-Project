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
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
        }
        .chat-card {
            display: flex;
            flex-direction: column;
            height: 75vh;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            background-color: #fff;
            overflow: hidden;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            flex-shrink: 0;
            background-color: #fd7e14; 
            color: white;
            border-bottom: 1px solid #e67311; 
        }

        .chat-header h6 {
            color: white; 
        }
        
        .chat-header small {
            color: #ffedd5; 
        }

        .chat-body {
            flex-grow: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background-color: #F0F0F0;
        }
        .chat-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            background-color: #F9F9F9;
            flex-shrink: 0;
        }
        .message-row {
            display: flex;
            margin-bottom: 1.25rem;
        }
        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .message-bubble {
            display: inline-block; 
            padding: 0.75rem 1.2rem;
            border-radius: 18px;
            max-width: 75%;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message-row.sent {
            justify-content: flex-start;
        }
        .message-row.sent .message-bubble {
            background-color: #D9D9D9;
            color: #333;
            border-top-left-radius: 4px;
        }
        
        .message-row.received {
            justify-content: flex-end;
        }
        .message-row.received .message-bubble {
            background-color: #fd7e14;
            color: white;
            border-top-right-radius: 4px;
        }

        .chat-footer .form-control {
            border: none;
            background-color: transparent;
            box-shadow: none;
            border-radius: 0;
            border-bottom: 1px solid #ced4da;
            padding-left: 0;
            padding-right: 0;
        }
        .chat-footer .form-control:focus {
            border-color: #fd7e14;
        }

        .chat-footer .btn-send {
            background: none;
            border: none;
            color: #fd7e14;
            font-size: 1.5rem;
            padding: 0;
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
                        <small><i class="bi bi-circle-fill" style="font-size: 0.6rem; color: #28a745;"></i> Online</small>
                    </div>
                </div>

                <div class="chat-body" id="chat-box">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="message-row <?php echo $row['sender'] == 'user' ? 'received' : 'sent'; ?>">
                            <div class="message-bubble">
                                <?= nl2br(htmlspecialchars($row['message'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="chat-footer">
                    <form method="POST" action="chat.php" class="d-flex align-items-center">
                        <input type="text" name="message" class="form-control" placeholder="Tulis pesan" required autocomplete="off">
                        <button class="btn btn-send ms-3 flex-shrink-0" type="submit">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.getElementById('chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>
</body>
</html>