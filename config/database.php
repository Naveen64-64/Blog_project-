<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "blog";

// Try direct connection to the database
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    // If direct connection fails (likely because database doesn't exist),
    // connect to MySQL server to set up the DB and tables.
    $conn_server = @mysqli_connect($db_host, $db_user, $db_pass);
    if (!$conn_server) {
        die("Database Connection Failed: " . mysqli_connect_error());
    }

    // Create database if not exists
    $sql_db = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    mysqli_query($conn_server, $sql_db);
    mysqli_close($conn_server);

    // Reconnect to the newly created database
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        die("Database Connection Failed after auto-setup: " . mysqli_connect_error());
    }
}

// Make sure users table exists
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB";
mysqli_query($conn, $sql_users);

// Make sure posts table exists
$sql_posts = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB";
mysqli_query($conn, $sql_posts);

// Check if posts table is missing the user_id column (if it already existed before our changes)
$result_col = mysqli_query($conn, "SHOW COLUMNS FROM posts LIKE 'user_id'");
if (mysqli_num_rows($result_col) == 0) {
    // Add user_id column as nullable so existing posts don't cause errors
    mysqli_query($conn, "ALTER TABLE posts ADD COLUMN user_id INT NULL");
    
    // Add foreign key constraint to link with users table
    mysqli_query($conn, "ALTER TABLE posts ADD CONSTRAINT fk_posts_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
}

// Check if posts table is missing the created_at column
$result_created = mysqli_query($conn, "SHOW COLUMNS FROM posts LIKE 'created_at'");
if (mysqli_num_rows($result_created) == 0) {
    mysqli_query($conn, "ALTER TABLE posts ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
}

?>