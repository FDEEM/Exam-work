<?php
session_start();

// include database connection and other necessary files
include('db.php');

// check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // redirect user to login page
    header("Location: login.php");
    exit();
}

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // process the form data
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // define upload directory for images and videos
    $uploadDirectory = "uploads/";

    // initialize file path variables
    $image_path = "";
    $video_path = "";

    // check if image file is uploaded
    if (!empty($_FILES['image']['tmp_name'])) {
        $image_name = basename($_FILES['image']['name']);
        $image_path = $uploadDirectory . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // check if video file is uploaded
    if (!empty($_FILES['video']['tmp_name'])) {
        $video_name = basename($_FILES['video']['name']);
        $video_path = $uploadDirectory . $video_name;
        move_uploaded_file($_FILES['video']['tmp_name'], $video_path);
    }

    // insert data into the database
    $insert_query = "INSERT INTO guides (title, content, image_path, video_path, user_id, game_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    // bind parameters with proper types
    mysqli_stmt_bind_param($stmt, "ssssii", $title, $content, $image_path, $video_path, $_SESSION['user_id'], $_POST['game_id']);
    if (mysqli_stmt_execute($stmt)) {
        // guide created successfully, redirect to guides page for the game
        $game_id = $_POST['game_id'];
        header("Location: game.php?id=$game_id");
        exit();
    } else {
        echo "Error creating guide: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Guide</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="container">
    <h1>Create Guide</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="hidden" name="game_id" value="<?php echo $_GET['game_id']; ?>">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br>
        <label for="content">Content:</label><br>
        <textarea style="max-width: 994px;" id="content" name="content" rows="4" cols="50" required></textarea><br>
        <label for="image">Upload Image:</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br>
        <label for="video">Upload Video:</label><br>
        <input type="file" id="video" name="video" accept="video/*"><br>
        <input type="submit" value="Submit">
        <a href="game.php?id=<?php echo $_GET['game_id']; ?>" class="cancel-btn">Cancel</a>
    </form>
</div>

</body>
</html>
