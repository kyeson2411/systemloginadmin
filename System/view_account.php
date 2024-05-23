<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'db.php';

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Account Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Account Details</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Credits</th>
                    <th>Expires At</th>
                    <th>Time Remaining</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($user): ?>
                    <?php
                    $expires_at = strtotime($user['expires_at']);
                    $remaining_time = $expires_at - time();
                    ?>
                    <tr id="user-<?php echo htmlspecialchars($user['id']); ?>" data-expiration="<?php echo $expires_at; ?>" class="<?php echo $remaining_time <= 0 ? 'hidden' : ''; ?>">
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['credits']); ?></td>
                        <td><?php echo htmlspecialchars($user['expires_at']); ?></td>
                        <td id="timer-<?php echo htmlspecialchars($user['id']); ?>"><?php echo gmdate("H:i:s", $remaining_time); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="credit_loading.php" class="btn btn-primary">Load More Credits</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function updateTimer() {
            document.querySelectorAll('tr[id^="user-"]').forEach(function(row) {
                var id = row.id.split('-')[1];
                var expirationTime = row.getAttribute('data-expiration') * 1000; // Convert to milliseconds
                var now = new Date().getTime();
                var distance = expirationTime - now;

                if (distance <= 0) {
                    row.classList.add('hidden');
                } else {
                    var hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                    var minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                    var seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

                    document.getElementById('timer-' + id).innerText = hours + ":" + minutes + ":" + seconds;
                }
            });
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    </script>
</body>
</html>
