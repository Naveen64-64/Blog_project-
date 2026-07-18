<?php
$page_title = "Edit Post";
$active_page = "posts";
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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$error = "";

// Fetch post to check existence and ownership
$stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$post) {
    // Post not found
    header("Location: viewposts.php");
    exit();
}

// Authorization check: Make sure this post belongs to the logged-in user
if ($post['user_id'] != $user_id) {
    die("Access Denied: You do not have permission to edit this post.");
}

if (isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Please fill in all fields.";
    } else {
        // Secure update with prepared statement
        $update_stmt = mysqli_prepare($conn, "UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($update_stmt, "ssii", $title, $content, $id, $user_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            mysqli_stmt_close($update_stmt);
            header("Location: viewposts.php");
            exit();
        } else {
            $error = "Error updating post: " . mysqli_error($conn);
        }
        mysqli_stmt_close($update_stmt);
    }
}

include "../config/header.php";
?>

<div class="form-container" style="max-width: 600px; margin: 40px auto;">
    <div class="form-card p-4 p-md-5 rounded-4 shadow-lg" style="background: var(--bg-glass); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.6);">
        <h2 class="text-center fw-bold mb-1">Edit Post</h2>
        <p class="form-subtitle text-center text-secondary mb-4 small">Make changes to your published article</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2.5 px-3 d-flex align-items-center gap-2 border border-danger-subtle rounded-2 mb-3">
                <i class="fa-solid fa-circle-exclamation"></i> <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="edit.php?id=<?php echo $id; ?>">
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold small text-dark mb-1">Post Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control py-2.5 px-3 rounded-2 shadow-none border" 
                    value="<?php echo isset($title) ? htmlspecialchars($title) : htmlspecialchars($post['title']); ?>"
                    required
                >
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-semibold small text-dark mb-1">Content</label>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-control py-2.5 px-3 rounded-2 shadow-none border" 
                    style="min-height: 200px;"
                    required
                ><?php echo isset($content) ? htmlspecialchars($content) : htmlspecialchars($post['content']); ?></textarea>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <button type="submit" name="update" class="btn btn-primary w-100 py-2.5 rounded-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> Save Changes
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