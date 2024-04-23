<?php
session_start();

// include database connection
include('db.php');

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // redirect user to login page
    header("Location: login.php");
    exit();
}

// fetch game details from the database if 'id' key exists in $_GET array
if(isset($_GET['id'])) {
    $game_id = $_GET['id'];
    $game_query = mysqli_query($conn, "SELECT * FROM games WHERE id=$game_id");
    $game = mysqli_fetch_assoc($game_query);
    
    // check if database connection is successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // fetch guides created by the current user for the current game
    $current_user_id = $_SESSION['user_id'];
    $currentUserGuidesQuery = "SELECT * FROM guides WHERE game_id=$game_id AND user_id=$current_user_id";
    $currentUserGuidesResult = mysqli_query($conn, $currentUserGuidesQuery);
}


// fetch guides created by other users for the current game
if(isset($game_id) && isset($current_user_id)) {
    $otherUsersGuidesQuery = "SELECT * FROM guides WHERE game_id=$game_id AND user_id!=$current_user_id";
    $otherUsersGuidesResult = mysqli_query($conn, $otherUsersGuidesQuery);
}

// editing a guide
if (isset($_POST['edit_guide'])) {
    $guide_id = $_POST['guide_id'];
    $new_title = $_POST['new_title'];
    $new_content = $_POST['new_content'];

    // update the guide in the database
    $update_query = "UPDATE guides SET title=?, content=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ssi", $new_title, $new_content, $guide_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Guide updated successfully";
    } else {
        echo "Error updating guide: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);

    // redirect back to the game page after processing the action
    header("Location: game.php?id=$game_id");
    exit();
}

// deleting a guide
if (isset($_POST['delete_guide'])) {
    $guide_id = $_POST['guide_id'];

    // delete the guide from the database
    $delete_query = "DELETE FROM guides WHERE id=?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $guide_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Guide deleted successfully";
    } else {
        echo "Error deleting guide: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);

    // redirect back to the game page after processing the action
    header("Location: game.php?id=$game_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($game['title']) ? $game['title'] : 'Game Guide'; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="container">
    <h1><?php echo isset($game['title']) ? $game['title'] : 'Game Guide'; ?></h1>
    <div class="navbar">
        <a href="index.php">Home</a>
        
    </div>
    <p style="color: white"><?php echo isset($game['description']) ? $game['description'] : ''; ?></p>

    <!-- create guide button -->
    <div class="create-guide-button">
        <a href="create_guide.php?game_id=<?php echo isset($game_id) ? $game_id : ''; ?>" class="btn">Create Guide</a>
    </div>

    <h2>Your Guides</h2>
    <div class="guides-list">
        <?php
        // display guides created by the current user
        if(isset($currentUserGuidesResult)) {
            while ($guide = mysqli_fetch_assoc($currentUserGuidesResult)) {
                echo "<div class='guide'>";
                echo "<h3>{$guide['title']}</h3>";
                echo "<p>{$guide['content']}</p>";
                // display image if available
                if (!empty($guide['image_path'])) {
                    echo "<img src='{$guide['image_path']}' alt='Guide Image'>";
                }
                // display video if available
                if (!empty($guide['video_path'])) {
                    echo "<video controls>";
                    echo "<source src='{$guide['video_path']}' type='video/mp4'>";
                    echo "Your browser does not support the video tag.";
                    echo "</video>";
                }
                // add edit and delete buttons for guides created by the current user
                echo "<form method='post'>";
                echo "<input type='hidden' name='guide_id' value='{$guide['id']}'>";
                echo "<a class='edit-btn' href='edit_guide.php?guide_id={$guide['id']}'>Edit</a>";
                echo "<input type='submit' name='delete_guide' value='Delete'>";
                echo "</form>";
                echo "</div>";
            }
        }
        ?>
    </div>

    <h2>Other Users' Guides</h2>
    <div class="guides-list">
        <?php
        // display guides created by other users
        if(isset($otherUsersGuidesResult)) {
            while ($guide = mysqli_fetch_assoc($otherUsersGuidesResult)) {
                echo "<div class='guide'>";
                echo "<h3>{$guide['title']}</h3>";
                echo "<p>{$guide['content']}</p>";
                // display image if available
                if (!empty($guide['image_path'])) {
                    echo "<img src='{$guide['image_path']}' alt='Guide Image'>";
                }
                // display video if available
                if (!empty($guide['video_path'])) {
                    echo "<video controls>";
                    echo "<source src='{$guide['video_path']}' type='video/mp4'>";
                    echo "Your browser does not support the video tag.";
                    echo "</video>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
