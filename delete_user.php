<?php
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);

    if ($user_id > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
}

$conn->close();
?>
