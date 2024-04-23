<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the logged out page
header("Location: logged_out.php");
exit();
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>