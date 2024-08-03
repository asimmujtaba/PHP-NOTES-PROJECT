<?php
include 'hello.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM notes_data WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: index.php?delete=success");
            exit();
        } else {
            header("Location: index.php?delete=fail&error=" . urlencode($stmt->error));
            exit();
        }
        $stmt->close();
    } else {
        header("Location: index.php?delete=fail&error=" . urlencode($conn->error));
        exit();
    }
}
$conn->close();
?>
