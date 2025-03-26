<?php
include 'db.php'; 


$sql = "SELECT * FROM users WHERE user_type = 'User' ORDER BY id ASC";

$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode(['success' => true, 'users' => $users]);

$conn->close();
?>
