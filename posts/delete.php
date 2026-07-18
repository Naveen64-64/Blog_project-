<?php
include "../config/database.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Restrict to logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if ($id > 0) {
    // Delete the post, ensuring it belongs to the logged-in user (authorization)
    $stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Redirect back to the posts feed
header("Location: viewposts.php");
exit();
?>