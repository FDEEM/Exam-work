<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // check if username and password are empty
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=emptyfields");
        exit();
    } else {
        // prepare SQL statement to fetch user from database
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: login.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                // verify password
                $password_check = password_verify($password, $row['password']);
                if ($password_check == false) {
                    header("Location: login.php?error=wrongpassword");
                    exit();
                } else if ($password_check == true) {
                    // login successful, start session
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    header("Location: index.php?login=success");
                    exit();
                }
            } else {
                header("Location: login.php?error=nouser");
                exit();
            }
        }
    }
} else {
    header("Location: login.php");
    exit();
}
?>
