<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addCredits($username, $credits) {
        $minutes = $credits * 6;
        $expires_at = date('Y-m-d H:i:s', strtotime("+$minutes minutes"));

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $this->conn->prepare("UPDATE users SET credits = credits + ?, expires_at = ? WHERE username = ?");
            $stmt->bind_param("dss", $credits, $expires_at, $username);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO users (username, credits, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $username, $credits, $expires_at);
        }

        if (!$stmt->execute()) {
            error_log("Error adding/updating credits for $username: " . $stmt->error);
        }

        $stmt->close();
    }

    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            return password_verify($password, $user['password']);
        }
        return false;
    }
}
?>
