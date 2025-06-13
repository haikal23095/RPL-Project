<?php
header('Content-Type: application/json');
include '../db.php';

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = mysqli_real_escape_string($kon, $_GET['user_id']);
$query = "SELECT sender, message, timestamp FROM messages WHERE user_id='$user_id' ORDER BY timestamp ASC";
$result = mysqli_query($kon, $query);
$messages = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['message'] = htmlspecialchars($row['message']);
        $messages[] = $row;
    }
}
echo json_encode($messages);
?>