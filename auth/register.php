<?php
$page_title = "Register";
$active_page = "register";
$path_prefix = "../";

include "../config/database.php";

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if username is already taken using prepared statement
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Username is already taken.";
        } else {
            // Hash password and insert user using prepared statement
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "ss", $username, $hash);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $success = "Registration successful! You can now <a href='login.php'>Login</a>.";
            } else {
                $error = "An error occurred during registration. Please try again.";
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}

include "../config/header.php";
?>

<div class="form-container" style="max-width: 480px; margin: 40px auto;">
    <div class="form-card p-4 p-md-5 rounded-4 shadow-lg" style="background: var(--bg-glass); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.6);">
        <h2 class="text-center fw-bold mb-1">Register</h2>
        <p class="form-subtitle text-center text-secondary mb-4 small">Create an account to start publishing articles</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2.5 px-3 d-flex align-items-center gap-2 border border-danger-subtle rounded-2 mb-3">
                <i class="fa-solid fa-circle-exclamation"></i> <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2.5 px-3 d-flex align-items-center gap-2 border border-success-subtle rounded-2 mb-3">
                <i class="fa-solid fa-circle-check"></i> <span><?php echo $success; ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small text-dark mb-1">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control py-2.5 px-3 rounded-2 shadow-none border" 
                    placeholder="Enter your username" 
                    value="<?php echo isset($username) && empty($success) ? htmlspecialchars($username) : ''; ?>"
                    required
                >
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small text-dark mb-1">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control py-2.5 px-3 rounded-2 shadow-none border" 
                    placeholder="Enter secure password" 
                    required
                >
            </div>
            
            <button type="submit" name="register" class="btn btn-primary py-2.5 w-100 rounded-2 d-flex align-items-center justify-content-center gap-2 mb-3">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <div class="form-footer text-center text-muted small">
            Already have an account? <a href="login.php" class="text-primary fw-semibold text-decoration-none">Log In</a>
        </div>
    </div>
</div>

<?php
include "../config/footer.php";
?>