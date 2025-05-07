<?php
session_start();
include('connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = htmlspecialchars($_POST['item_name']);
    $item_type = htmlspecialchars($_POST['item_type']);
    $location_found = htmlspecialchars($_POST['location_found']);
    $description = htmlspecialchars($_POST['description']);
    $date_found = date("Y-m-d"); // auto-date
    $user_id = $_SESSION['user_id'];

    // Handle image upload
    $photo = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir); // create if doesn't exist
        }

        $filename = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_path = $upload_dir . $filename;
        $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed)) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_path)) {
                $photo = $filename;
            } else {
                $message = "Error uploading image.";
            }
        } else {
            $message = "Only JPG, JPEG, PNG, GIF files allowed.";
        }
    }

    // Save to DB if no upload error
    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO found_items (id, item_name, item_type, location_found, description, photo, date_found) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $item_name, $item_type, $location_found, $description, $photo, $date_found);

        if ($stmt->execute()) {
            header("Location: userdashboard.php?submitted=1");
            exit;
        } else {
            $message = "Failed to submit item.";
        }
    }
}
?>

<!-- HTML form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Found Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h2>Submit Found Item</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Item Name</label>
            <input type="text" name="item_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Item Type</label>
            <input type="text" name="item_type" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Location Found</label>
            <input type="text" name="location_found" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Description (optional)</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label>Photo (optional)</label>
            <input type="file" name="photo" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-success">Submit Item</button>
        <a href="userdashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>
