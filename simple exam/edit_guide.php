<?php
session_start();

// Include database connection
include('db.php');

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect user to login page
    header("Location: login.php");
    exit();
}

// Check if guide_id is provided in the URL
if (!isset($_GET['guide_id'])) {
    // Redirect user to game page if guide_id is not provided
    header("Location: game.php");
    exit();
}

// Fetch guide details from the database
$guide_id = $_GET['guide_id'];
$guide_query = mysqli_query($conn, "SELECT * FROM guides WHERE id=$guide_id");
$guide = mysqli_fetch_assoc($guide_query);

// Check if the guide exists and belongs to the current user
if (!$guide || $guide['user_id'] != $_SESSION['user_id']) {
    // Redirect user to game page if the guide doesn't exist or doesn't belong to the current user
    header("Location: game.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data
    $new_title = $_POST['new_title'];
    $new_content = $_POST['new_content'];
    $image_path = $guide['image_path']; // Default to existing image path
    $video_path = $guide['video_path']; // Default to existing video path

    // Check if file was uploaded for image
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
        // Process the uploaded image
        $image_name = $_FILES['new_image']['name'];
        $image_tmp_name = $_FILES['new_image']['tmp_name'];
        $image_path = "uploads/" . $image_name; // Adjust path as needed

        // Move uploaded image to the uploads directory
        move_uploaded_file($image_tmp_name, $image_path);
    } elseif (isset($_POST['remove_image'])) {
        // Remove image if remove image button is clicked
        $image_path = ''; // Empty the image path
    }

    // Check if file was uploaded for video
    if (isset($_FILES['new_video']) && $_FILES['new_video']['error'] === UPLOAD_ERR_OK) {
        // Process the uploaded video
        $video_name = $_FILES['new_video']['name'];
        $video_tmp_name = $_FILES['new_video']['tmp_name'];
        $video_path = "uploads/" . $video_name; // Adjust path as needed

        // Move uploaded video to the uploads directory
        move_uploaded_file($video_tmp_name, $video_path);
    }

    // Update the guide in the database
    $update_query = "UPDATE guides SET title=?, content=?, image_path=?, video_path=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssi", $new_title, $new_content, $image_path, $video_path, $guide_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Guide updated successfully";
        // Redirect back to the game page after processing the action
        header("Location: game.php?id={$guide['game_id']}");
        exit();
    } else {
        echo "Error updating guide: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guide</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="container">
    <h1>Edit Guide</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?guide_id=<?php echo $guide_id; ?>" enctype="multipart/form-data">
        <input type="hidden" name="guide_id" value="<?php echo $guide_id; ?>">
        <label for="new_title">New Title:</label><br>
        <input type="text" id="new_title" name="new_title" value="<?php echo $guide['title']; ?>" required><br>
        <label for="new_content">New Content:</label><br>
        <textarea style="max-width: 994px;" id="new_content" name="new_content" rows="4" cols="50" required><?php echo $guide['content']; ?></textarea><br>
        <label for="new_image">New Image:</label><br>
        <input type="file" id="new_image" name="new_image"><br>
        <?php if (!empty($guide['image_path'])): ?>
            <label for="remove_image">Remove Image:</label>
            <input type="checkbox" id="remove_image" name="remove_image" value="1"><br>
            <img src="<?php echo $guide['image_path']; ?>" alt="Current Image"><br>
        <?php endif; ?>
        <label for="new_video">New Video:</label><br>
        <input type="file" id="new_video" name="new_video"><br>
        <input type="submit" value="Save">
        <a href="game.php?id=<?php echo $guide['game_id']; ?>" class="cancel-btn">Cancel</a>
    </form>
</div>

</body>
</html>
