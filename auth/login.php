<?php
$page_title = "Login";
$active_page = "login";
$path_prefix = "../";

include "../config/database.php";

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Secure query with prepared statements
        $stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Verify hashed password
            if (password_verify($password, $user['password'])) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['id'];
                
                header("Location: ../posts/viewposts.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
        mysqli_stmt_close($stmt);
    }
}

include "../config/header.php";
?>

<div class="form-container" style="max-width: 480px; margin: 40px auto;">
    <div class="form-card p-4 p-md-5 rounded-4 shadow-lg" style="background: var(--bg-glass); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.6);">
        <h2 class="text-center fw-bold mb-1">Login</h2>
        <p class="form-subtitle text-center text-secondary mb-4 small">Welcome back! Log in to manage your posts</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2.5 px-3 d-flex align-items-center gap-2 border border-danger-subtle rounded-2 mb-3">
                <i class="fa-solid fa-circle-exclamation"></i> <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold small text-dark mb-1">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control py-2.5 px-3 rounded-2 shadow-none border" 
                    placeholder="Enter your username" 
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
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
                    placeholder="Enter your password" 
                    required
                >
            </div>
            
            <button type="submit" name="login" class="btn btn-primary py-2.5 w-100 rounded-2 d-flex align-items-center justify-content-center gap-2 mb-3">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>
        </form>
        
        <div class="form-footer text-center text-muted small">
            Don't have an account? <a href="register.php" class="text-primary fw-semibold text-decoration-none">Register now</a>
        </div>
    </div>
</div>

<?php
include "../config/footer.php";
?>