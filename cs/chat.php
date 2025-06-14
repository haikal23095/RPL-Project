<?php
session_start();
include '../db.php'; // Pastikan path ini benar
$page = 'cs';

if (!isset($_SESSION['cs'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>CUSTOMER SERVICE CHAT</title>
    
    <link href="../assets/img/logo_CasaLuxe.png" rel="icon">
    <link href="../assets/img/logo_CasaLuxe.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        /* Custom Styles for New Chat UI */
        .main-chat-wrapper {
            position: relative;
            height: calc(100vh - 150px);
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .chat-view {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            transition: transform 0.4s ease-in-out;
            z-index: 10;
        }

        .list-view {
            transition: transform 0.4s ease-in-out;
        }

        /* Animations */
        .list-view.slide-up {
            transform: translateY(-100%);
        }
        .chat-view.slide-down {
            transform: translateY(0);
        }
        .chat-view.hidden {
            transform: translateY(-100%);
        }

        /* Header Styling */
        .chat-header {
            background-color: #fd7e14; /* Orange color */
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
        }
        .chat-header h5 {
            margin: 0;
        }
        .back-button {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            cursor: pointer;
        }

        /* User List Styling */
        .user-list-container {
            overflow-y: auto;
            height: calc(100vh - 220px);
        }
        .user-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            cursor: pointer;
            border-bottom: 1px solid #dee2e6;
        }
        .user-item:hover {
            background-color: #f1f1f1;
        }
        .user-item .avatar {
            font-size: 1.5rem;
            color: #6c757d;
            margin-right: 1rem;
        }
        .user-item .details {
            flex-grow: 1;
        }
        .user-item .details h6 {
            margin: 0;
            font-weight: 600;
        }
        .user-item .meta {
            text-align: right;
            font-size: 0.8rem;
            color: #6c757d;
        }
        .unread-badge {
            background-color: #fd7e14;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
            margin-top: 5px;
        }
        
        /* Chat Messages Styling */
        .chat-messages {
            flex-grow: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background-color: #e9ecef;
        }
        .message {
            display: flex;
            margin-bottom: 1rem;
        }
        .message .bubble {
            padding: 0.75rem 1rem;
            border-radius: 18px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .message.sent {
            justify-content: flex-end;
        }
        .message.sent .bubble {
            background-color: #fd7e14;
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message.received {
            justify-content: flex-start;
        }
        .message.received .bubble {
            background-color: #fff;
            color: #333;
            border: 1px solid #dee2e6;
            border-bottom-left-radius: 4px;
        }

        /* Message Input */
        .message-input-area {
            padding: 1rem;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }
        .message-input-area .form-control {
            border-radius: 20px;
            border-color: transparent;
            background-color: #f1f1f1;
        }
        .message-input-area .btn-send {
            background: none;
            border: none;
            color: #fd7e14;
            font-size: 1.5rem;
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
                    <li class="breadcrumb-item"><a href="chat.php">Home</a></li>
                    <li class="breadcrumb-item active">Pelayanan Pelanggan</li>
                </ol>
            </nav>
        </div><section class="section">
            <div class="card">
                <div class="card-body p-0">
                    <div class="main-chat-wrapper">
                        
                        <div class="list-view" id="list-view">
                            <div class="chat-header">
                                <i class="bi bi-headset me-2"></i>
                                <h5 class="flex-grow-1">Pelayanan Pelanggan</h5>
                            </div>
                            <div class="p-2">
                                <input type="text" class="form-control" placeholder="Cari...">
                            </div>
                            <div class="user-list-container" id="user-list">
                                </div>
                        </div>

                        <div class="chat-view hidden" id="chat-view">
                            <div class="chat-header">
                                <i class="bi bi-arrow-left back-button" id="back-button"></i>
                                <h5 id="chat-with-user">Nama Pengguna</h5>
                            </div>
                            <div class="chat-messages" id="chat-messages">
                                </div>
                            <div class="message-input-area">
                                <form id="chat-form">
                                    <div class="input-group align-items-center">
                                        <input type="text" id="message-input-field" class="form-control" placeholder="Tulis pesan" autocomplete="off" disabled>
                                        <button class="btn btn-send" type="submit" id="send-button" disabled>
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
    $(document).ready(function() {
        let currentUserId = null;
        let chatInterval = null;

        // Fungsi untuk memuat daftar pengguna
        function loadUsers() {
            $.getJSON('api_get_users.php', function(data) {
                const userList = $('#user-list');
                userList.empty();
                if (data.length === 0) {
                    userList.html('<div class="p-3 text-center text-muted">Tidak ada percakapan.</div>');
                    return;
                }
                data.forEach(user => {
                    // NOTE: Last message, time, and unread count need backend changes.
                    // For now, we will build the UI structure without that dynamic data.
                    const userElement = `
                        <div class="user-item" data-user-id="${user.id_user}" data-user-name="${user.nama}">
                            <div class="avatar"><i class="bi bi-person-circle"></i></div>
                            <div class="details">
                                <h6>${user.nama}</h6>
                                <small class="text-muted">Klik untuk melihat percakapan</small>
                            </div>
                            <div class="meta">
                                <span>27/10</span>
                                </div>
                        </div>`;
                    userList.append(userElement);
                });
            });
        }

        // Fungsi untuk memuat pesan
        function loadMessages() {
            if (!currentUserId) return;
            $.getJSON(`api_get_messages.php?user_id=${currentUserId}`, function(messages) {
                const chatBox = $('#chat-messages');
                const scrollAtBottom = chatBox.scrollTop() + chatBox.innerHeight() >= chatBox[0].scrollHeight - 20;
                
                chatBox.empty();
                messages.forEach(msg => {
                    const messageClass = msg.sender === 'admin' ? 'sent' : 'received';
                    const messageElement = `<div class="message ${messageClass}"><div class="bubble">${msg.message}</div></div>`;
                    chatBox.append(messageElement);
                });

                if(scrollAtBottom) {
                    chatBox.scrollTop(chatBox[0].scrollHeight);
                }
            });
        }
        
        // --- Event Handlers ---

        // Klik user dari daftar untuk membuka chat
        $('#user-list').on('click', '.user-item', function() {
            currentUserId = $(this).data('user-id');
            const userName = $(this).data('user-name');

            $('#chat-with-user').text(userName);
            $('#message-input-field, #send-button').prop('disabled', false);

            $('#list-view').addClass('slide-up');
            $('#chat-view').removeClass('hidden').addClass('slide-down');
            
            if (chatInterval) clearInterval(chatInterval);
            loadMessages();
            chatInterval = setInterval(loadMessages, 3000);
            
            setTimeout(() => { $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight); }, 400);
        });

        // Klik tombol kembali
        $('#back-button').on('click', function() {
            if (chatInterval) clearInterval(chatInterval);
            currentUserId = null;
            
            $('#list-view').removeClass('slide-up');
            $('#chat-view').removeClass('slide-down').addClass('hidden');
            
            $('#message-input-field, #send-button').prop('disabled', true);
            $('#chat-messages').empty();
            $('#chat-with-user').text('Pilih Percakapan');
        });

        // Mengirim pesan
        $('#chat-form').on('submit', function(e) {
            e.preventDefault();
            const message = $('#message-input-field').val().trim();
            if (!message || !currentUserId) return;

            // Optimistic UI update
            const sentMessageElement = `<div class="message sent"><div class="bubble">${message}</div></div>`;
            $('#chat-messages').append(sentMessageElement);
            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

            const originalMessage = $('#message-input-field').val();
            $('#message-input-field').val('');

            $.post('api_send_message.php', { user_id: currentUserId, message: originalMessage }, function(response) {
                if (response.status !== 'success') {
                    alert('Gagal mengirim pesan.');
                    // Optional: remove the optimistic message or show an error
                }
            }, 'json');
        });
        
        // Muat daftar pengguna pertama kali
        loadUsers();
    });
    </script>
</body>
</html>