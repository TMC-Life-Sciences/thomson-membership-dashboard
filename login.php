<?php
require __DIR__ . '/config/config.inc.php';
require __DIR__ . '/config/session.php';
secure_session_start($conn);

if (isset($_POST['email']) && isset($_POST['pass'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($_POST['email']);
    $pass = validate($_POST['pass']);

    if (empty($email)) {
        echo "<script>window.alert('Email is required')</script>";
        echo "<script>window.location.replace('index.php')</script>";
        exit();
    } elseif (empty($pass)) {
        echo "<script>window.alert('Password is required')</script>";
        echo "<script>window.location.replace('index.php')</script>";
        exit();
    } else {
        $attempt_limit = 5;
        $lockout_duration = 15 * 60; // 15 minutes in seconds
        $current_time = time();

        $sql = "SELECT * FROM `user` WHERE username= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Check if account is locked
            if (
                $row['failed_attempts'] >= $attempt_limit &&
                ($current_time - strtotime($row['lockout_time'])) < $lockout_duration
            ) {
                echo "<script>window.alert('Account locked. Try again later.')</script>";
                echo "<script>window.location.replace('index.php')</script>";
                exit();
            }

            $pass = hash('sha256', $pass); // Hashing password

            if ($row['username'] === $email && $row['password'] === $pass) {
                // Successful login: reset failed attempts and lockout time
                $reset_sql = "UPDATE `user` SET failed_attempts = 0, lockout_time = NULL WHERE username = ?";
                $reset_stmt = $conn->prepare($reset_sql);
                $reset_stmt->bind_param("s", $email);
                $reset_stmt->execute();

                // Set session variables
                $_SESSION['username'] = $row['username'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['userid'] = $row['userid'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['department'] = $row['department'];
                $_SESSION['initial'] = $row['initial'];

                if ($_SESSION['initial'] == 1) {
                    $u = encryptor('encrypt', $_SESSION['username']);
                    header("Location: initial.php?username=$u");
                    exit();
                } else {
                    secure_session_start($conn, $_SESSION['username']);
                    header("Location: main.php");
                    exit();
                }
            } else {
                // Failed login attempt
                $failed_attempts = $row['failed_attempts'] + 1;
                if ($failed_attempts >= $attempt_limit) {
                    // Lock account and set lockout time
                    $lockout_sql = "UPDATE `user` SET failed_attempts = ?, lockout_time = NOW() WHERE username = ?";
                    $lockout_stmt = $conn->prepare($lockout_sql);
                    $lockout_stmt->bind_param("is", $failed_attempts, $email);
                    $lockout_stmt->execute();

                    echo "<script>window.alert('Too many attempts. Your Account is locked, Please contact your administrator')</script>";
                } else {
                    // Increment failed attempts without lockout
                    $increment_sql = "UPDATE `user` SET failed_attempts = ? WHERE username = ?";
                    $increment_stmt = $conn->prepare($increment_sql);
                    $increment_stmt->bind_param("is", $failed_attempts, $email);
                    $increment_stmt->execute();

                    echo "<script>window.alert('Incorrect username or password.')</script>";
                }
                echo "<script>window.location.replace('index.php')</script>";
                exit();
            }
        } else {
            echo "<script>window.alert('Incorrect email or password')</script>";
            echo "<script>window.location.replace('index.php')</script>";
            exit();
        }
    }
} else {
    header("Location: index.php");
    exit();
}
