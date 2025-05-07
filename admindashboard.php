<?php
session_start();
include('connection.php');

// Redirect to admin login if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Fixed redirection to the admin login page
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Use prepared statements to handle actions
    if ($_GET['action'] == 'approve') {
        $stmt = $conn->prepare("UPDATE items SET is_approved = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();  // Close statement
    } elseif ($_GET['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();  // Close statement
    }

    // Redirect back to the dashboard after action is performed
    header('Location: admindashboard.php');
    exit();
}

// Fetch unapproved items
$sql = "SELECT * FROM items WHERE is_approved = 0 ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        
        <?php if ($result->num_rows == 0): ?>
            <div class="alert alert-info">No unapproved items found.</div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Item Type</th>
                    <th>Description</th>
                    <th>Location Found</th>
                    <th>Date Found</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['location_found']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_found']); ?></td>
                        <td>
                            <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                            <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
