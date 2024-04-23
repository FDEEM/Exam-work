<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=passwordsdontmatch&username=" . $username);
        exit();
    } else {
        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: register.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: register.php?error=usernametaken");
                exit();
            } else {
                // Hash password before storing in database
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // Insert new user into database
                $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: register.php?error=sqlerror");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
                    mysqli_stmt_execute($stmt);
                    header("Location: register.php?signup=success");
                    exit();
                }
            }
        }
    }
} else {
    header("Location: register.php");
    exit();
}
?>
