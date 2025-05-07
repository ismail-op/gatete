<?php
include 'connection.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php include('connection.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Found Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Report Found Item</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" class="form-control" placeholder="Your Name" required><br>
        <input type="text" name="item_type" class="form-control" placeholder="Item Type" required><br>
        <textarea name="description" class="form-control" placeholder="Description" required></textarea><br>
        <input type="file" name="photo" class="form-control" required><br>
        <input type="text" name="location" class="form-control" placeholder="Where it was found" required><br>
        <input type="text" name="contact" class="form-control" placeholder="Your Contact Info" required><br>
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </form>

<?php
if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $type = htmlspecialchars($_POST['item_type']);
    $desc = htmlspecialchars($_POST['description']);
    $loc = htmlspecialchars($_POST['location']);
    $contact = htmlspecialchars($_POST['contact']);
    $date = date('Y-m-d');

    $photo = $_FILES['photo']['name'];
    $target = 'uploads/' . basename($photo);

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO items (name, item_type, description, photo, location_found, contact_info, date_found) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $type, $desc, $photo, $loc, $contact, $date);
        $stmt->execute();
        echo "<div class='alert alert-success mt-3'>Item submitted successfully. Awaiting admin approval.</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Failed to upload image.</div>";
    }
}
?>
</div>
</body>
</html>