<?php
header('Content-Type: application/json');
include '../db.php';

$query = "
    SELECT DISTINCT m.user_id, u.nama, u.id_user 
    FROM messages m
    JOIN user u ON m.user_id = u.id_user
    ORDER BY u.nama ASC
";
$result = mysqli_query($kon, $query);
$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
echo json_encode($users);
?>