<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['credits'])) {
    $username = $_SESSION['username'];
    $credits = floatval($_POST['credits']);
    $user->addCredits($username, $credits);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Credit Loading System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Credit Loading System</h2>
        <form action="" method="POST" class="form-inline mb-4">
            <div class="form-group mr-2">
                <label for="credits" class="mr-2">Credits:</label>
                <input type="number" step="0.01" id="credits" name="credits" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Load Credits</button>
        </form>

        <a href="view_account.php" class="btn btn-secondary">View Account Details</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
