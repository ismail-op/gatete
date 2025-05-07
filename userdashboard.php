<?php
// === user/dashboard.php ===
session_start();
include('connection.php');

// Fetch all approved items
$stmt = $conn->prepare("SELECT * FROM items WHERE is_approved = 1 ORDER BY date_found DESC");
$stmt->execute();
$result = $stmt->get_result();

$found_items = [];
$lost_items = [];

while ($row = $result->fetch_assoc()) {
    // Assuming 'item_type' can be either 'found' or 'lost'
    if (strtolower($row['item_type']) === 'found') {
        $found_items[] = $row;
    } else {
        $lost_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>User Dashboard</h2>

    <h3 class="mt-4">Found Items</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Photo</th>
                <th>Location</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($found_items as $item) { ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td><img src="../uploads/<?= htmlspecialchars($item['photo']) ?>" width="100"></td>
                    <td><?= htmlspecialchars($item['location_found']) ?></td>
                    <td><?= htmlspecialchars($item['date_found']) ?></td>
                </tr>
                <a href="submit_found_item.php" class="btn btn-success">Submit Found Item</a>

            <?php } ?>
        </tbody>
    </table>

    <h3 class="mt-5">Lost Items</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Photo</th>
                <th>Location</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lost_items as $item) { ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td><img src="../uploads/<?= htmlspecialchars($item['photo']) ?>" width="100"></td>
                    <td><?= htmlspecialchars($item['location_found']) ?></td>
                    <td><?= htmlspecialchars($item['date_found']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>