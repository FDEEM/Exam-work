<?php
session_start();
include('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Guide Website</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="container">
    <h1>Game Guide Website</h1>
    <div class="navbar">
        <a href="index.php">Home</a>
        <?php
        // check if user is logged in
        if (isset($_SESSION['user_id'])) {
            echo "<a href='logout.php'>Logout</a>";
        } else {
            echo "<a href='login.php'>Login</a>";
            echo "<a href='register.php'>Register</a>";
        }
        ?>
    </div>
    <h2>Games</h2>
    <div class="games-list">
        <?php
        // cetch games from the database
        $games = mysqli_query($conn, "SELECT * FROM games");

        // display list of games
        while ($game = mysqli_fetch_assoc($games)) {
            echo "<div class='game'>";
            echo "<h3><a href='game.php?id={$game['id']}'>{$game['title']}</a></h3>";
            echo "<p>{$game['description']}</p>";
            echo "</div>";
        }
        ?>
    </div>
</div>

</body>
</html>
