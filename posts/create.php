<?php
$page_title = "Create Post";
$active_page = "create";
$path_prefix = "../";

include "../config/database.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Restrict to logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$error = "";

if (isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $error = "Please fill in all fields.";
    } else {
        // Insert post using prepared statement
        $stmt = mysqli_prepare($conn, "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $title, $content);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: viewposts.php");
            exit();
        } else {
            $error = "Error adding post: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

include "../config/header.php";
?>

<div class="form-container" style="max-width: 600px; margin: 40px auto;">
    <div class="form-card p-4 p-md-5 rounded-4 shadow-lg" style="background: var(--bg-glass); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.6);">
        <h2 class="text-center fw-bold mb-1">Create Post</h2>
        <p class="form-subtitle text-center text-secondary mb-4 small">Share your insights and stories with the world</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2.5 px-3 d-flex align-items-center gap-2 border border-danger-subtle rounded-2 mb-3">
                <i class="fa-solid fa-circle-exclamation"></i> <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="create.php">
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold small text-dark mb-1">Post Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control py-2.5 px-3 rounded-2 shadow-none border" 
                    placeholder="Enter a catchy title" 
                    value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
                    required
                >
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-semibold small text-dark mb-1">Content</label>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-control py-2.5 px-3 rounded-2 shadow-none border" 
                    placeholder="Write your article here..." 
                    style="min-height: 200px;"
                    required
                ><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <button type="submit" name="submit" class="btn btn-primary w-100 py-2.5 rounded-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> Add Post
                    </button>
                </div>
                <div class="col-6">
                    <a href="viewposts.php" class="btn btn-secondary w-100 py-2.5 rounded-2 d-flex align-items-center justify-content-center border-secondary-subtle text-dark">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
include "../config/footer.php";
?>