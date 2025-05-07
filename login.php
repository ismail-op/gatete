<?php
session_start();
include('connection.php');

// Check if the admin is already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admindashboard.php');
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the login credentials
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Prepare and execute the query to get the admin details from the database
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password matches, set session variable
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id']; // Store admin's ID in session for further use

            // Redirect to the admin dashboard
            header('Location: admindashboard.php');
            exit();
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "Admin not found!";
    }
}
?>

<!-- HTML Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Admin Login</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

</body>
</html>
