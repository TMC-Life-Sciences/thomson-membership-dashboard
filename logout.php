<?php
require __DIR__ . '/config/config.inc.php';
require __DIR__ . '/config/session.php';

secure_session_start($conn); // Resume the session

if (isset($_GET['logout'])) {

    $param = $_GET['logout'];

    function destroy_session($conn)
    {
        $username = $_SESSION['username'];
        $session_id = session_id();
        $sql = "UPDATE `user` SET `session_id`= NULL WHERE `username` = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();
        }

        // Destroy the session
        $_SESSION = [];
        session_destroy();

        // Clear the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }

    destroy_session($conn);

    header("Location: index.php?logout=$param");
    exit();
}
