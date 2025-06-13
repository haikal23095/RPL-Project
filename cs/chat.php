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
        .chat-container { display: flex; height: calc(100vh - 200px); }
        .user-list { width: 30%; border-right: 1px solid #ddd; overflow-y: auto; }
        .chat-area { width: 70%; display: flex; flex-direction: column; }
        .chat-messages { flex-grow: 1; overflow-y: auto; padding: 20px; background-color: #f9f9f9; }
        .message-input { padding: 15px; border-top: 1px solid #ddd; }
        .user-item { cursor: pointer; transition: background-color 0.2s; }
        .user-item.active, .user-item:hover { background-color: #e9ecef; }
        .message { margin-bottom: 15px; display: flex; flex-direction: column; }
        .message .bubble { padding: 10px 15px; border-radius: 20px; max-width: 75%; word-wrap: break-word; }
        .message.sent .bubble { background-color: #0d6efd; color: white; align-self: flex-end; }
        .message.received .bubble { background-color: #e9ecef; color: #333; align-self: flex-start; }
    </style>
</head>

<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Dashboard Chat</h1>
        </div><section class="section">
            <div class="card">
                <div class="card-body p-0">
                    <div class="chat-container">
                        <div class="user-list list-group list-group-flush" id="user-list">
                            <div class="p-3 text-center text-muted">Memuat percakapan...</div>
                        </div>
                        <div class="chat-area" id="chat-area" style="display: none;">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="m-0" id="chat-with-user">Pilih Percakapan</h5>
                            </div>
                            <div class="chat-messages" id="chat-messages"></div>
                            <div class="message-input">
                                <form id="chat-form">
                                    <div class="input-group">
                                        <input type="text" id="message-input-field" class="form-control" placeholder="Ketik pesan..." disabled>
                                        <button class="btn btn-primary" type="submit" id="send-button" disabled>
                                            <i class="bi bi-send"></i> Kirim
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="no-chat-selected text-center p-5" id="no-chat-selected">
                            <h5 class="text-muted">Pilih pengguna untuk memulai percakapan</h5>
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

        function loadUsers() {
            $.getJSON('api_get_users.php', function(data) {
                const userList = $('#user-list');
                userList.empty();
                if (data.length === 0) {
                    userList.html('<div class="p-3 text-center text-muted">Tidak ada percakapan.</div>');
                    return;
                }
                data.forEach(user => {
                    const userElement = `
                        <a href="#" class="list-group-item list-group-item-action user-item" data-user-id="${user.id_user}" data-user-name="${user.nama}">
                            <h6 class="mb-1">${user.nama}</h6>
                            <small class="text-muted">User ID: ${user.id_user}</small>
                        </a>`;
                    userList.append(userElement);
                });
            });
        }

        function loadMessages() {
            if (!currentUserId) return;
            $.getJSON(`api_get_messages.php?user_id=${currentUserId}`, function(messages) {
                const chatBox = $('#chat-messages');
                chatBox.empty();
                messages.forEach(msg => {
                    const messageClass = msg.sender === 'admin' ? 'sent' : 'received';
                    const messageElement = `<div class="message ${messageClass}"><div class="bubble">${msg.message}</div></div>`;
                    chatBox.append(messageElement);
                });
                chatBox.scrollTop(chatBox[0].scrollHeight);
            });
        }
        
        $('#user-list').on('click', '.user-item', function(e) {
            e.preventDefault();
            
            $('#no-chat-selected').hide();
            $('#chat-area').show();
            currentUserId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            $('.user-item').removeClass('active');
            $(this).addClass('active');
            $('#chat-with-user').text(`Chat dengan ${userName}`);
            $('#message-input-field, #send-button').prop('disabled', false);
            
            if (chatInterval) clearInterval(chatInterval);
            loadMessages();
            chatInterval = setInterval(loadMessages, 3000);
        });

        $('#chat-form').on('submit', function(e) {
            e.preventDefault();
            const message = $('#message-input-field').val().trim();
            if (!message || !currentUserId) return;

            $.post('api_send_message.php', { user_id: currentUserId, message: message }, function(response) {
                if (response.status === 'success') {
                    $('#message-input-field').val('');
                    loadMessages();
                }
            }, 'json');
        });
        
        loadUsers();
    });
    </script>
</body>
</html>