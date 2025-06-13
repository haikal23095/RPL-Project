<?php
header('Content-Type: application/json');
session_start();
include '../db.php';

if (!isset($_SESSION['cs'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_POST['user_id']) || !isset($_POST['message']) || empty(trim($_POST['message']))) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

$user_id = mysqli_real_escape_string($kon, $_POST['user_id']);
$message = mysqli_real_escape_string($kon, $_POST['message']);
$sender = 'admin';

$query = "INSERT INTO messages (sender, user_id, message) VALUES ('$sender', '$user_id', '$message')";

if (mysqli_query($kon, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>