<?php
// Start or resume the session securely
function secure_session_start($conn, $username = null)
{
    $is_localhost = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1');
    if (session_status() === PHP_SESSION_NONE) {
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $cookieParams["lifetime"],
            'path' => $cookieParams["path"],
            'domain' => $cookieParams["domain"],
            'secure' => !$is_localhost,            // Ensure cookie is sent over HTTPS only
            'httponly' => false,                    // Prevent JavaScript access to the session ID cookie
            'samesite' => 'Strict'                 // Prevent cross-site cookie access
        ]);

        session_name('membership_session');
        session_start();

        // Regenerate session ID on first request to avoid fixation
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);  // Regenerate and delete old session ID
            $_SESSION['initiated'] = true;
        }
    }


    $_SESSION['last_activity'] = time();

    if ($username) {

        $session_id = session_id();
        $_SESSION['username'] = $username;

        $existingSessionId = NULL;
        $stmt = $conn->prepare("SELECT `session_id` FROM `user` WHERE `username` = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($existingSessionId);
        $stmt->fetch();
        $stmt->close();
        if ($existingSessionId && $existingSessionId !== session_id()) {
            echo
            "<script>
                var confirmLogout = confirm('You has already logged in on another device. Do you want to log out from the other device and proceed to login?');
                if (confirmLogout) {
                    window.location.replace('logout.php?logout=3');
                } else {
                    window.location.replace('index.php?logout=4');
                }   
            </script>";
            exit();
        } else {

            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            // Insert session details into the database
            $sql = "UPDATE `user` SET `last_login`=NOW(),`session_id`=?,`ip_address`=?,`user_agent`=? WHERE `username` = ?"; // Update last activity if session exists
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssss", $session_id, $ip_address, $user_agent, $username);
                if (!$stmt->execute()) {
                    error_log("Update failed: " . $stmt->error);  // Log any errors from the execution
                }
                $stmt->close();
            } else {
                // Log error if the statement fails
                error_log("Error storing session data: " . $conn->error);
            }
        }
    }
}

//validate session
function validate_session($conn)
{
    if (!isset($_SESSION['username']) || !session_id()) {
        return false; // User is not logged in
    }

    $session_id = session_id();
    $username = $_SESSION['username'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Check if session ID exists in the database
    $sql = "SELECT * FROM `user` WHERE session_id = ? AND username = ? AND ip_address = ? AND user_agent = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssss", $session_id, $username, $ip_address, $user_agent);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update last activity timestamp for session tracking
            $update_sql = "UPDATE `user` SET `last_login` = NOW() WHERE session_id = ?";
            $update_stmt = $conn->prepare($update_sql);

            if (!$update_stmt) {
                error_log("Database error on update: " . $conn->error);
                return false;
            }
            $update_stmt->bind_param("s", $session_id);
            $update_stmt->execute();
            $update_stmt->close();

            $stmt->close();
            return true; // Session is valid
        } else {
            $stmt->close();
            return false; // Session is invalid
        }
    } else {
        error_log("Error validating session: " . $conn->error);
        return false;
    }
}

// Check if user is logged in (example usage)
function is_logged_in()
{
    return isset($_SESSION['userid']) && isset($_SESSION['username']) && isset($_SESSION['role']) && isset($_SESSION['department']);
}
