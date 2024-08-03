<?php
include 'hello.php';

$success = false;
$error = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the current data
    $stmt = $conn->prepare("SELECT * FROM notes_data WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $note = $result->fetch_assoc();
        } else {
            echo "Note not found!";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
} else {
    echo "Invalid request!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Prepare and execute the update statement
    $stmt = $conn->prepare("UPDATE notes_data SET title = ?, description = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssi", $title, $description, $id);
        if ($stmt->execute()) {
            header("Location: index.php?update=success");
            exit();
        } else {
            $error = $stmt->error;
            header("Location: index.php?update=fail&error=" . urlencode($error));
            exit();
        }
        $stmt->close();
    } else {
        $error = $conn->error;
        header("Location: index.php?update=fail&error=" . urlencode($error));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Note</h2>
        <form action="update.php?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                <label for="title">Notes Title</label>
                <input type="text" class="form-control" name="title" id="title" value="<?php echo htmlspecialchars($note['title']); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" id="description"><?php echo htmlspecialchars($note['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Note</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
