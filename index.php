<?php
$page_title = "Home";
$active_page = "home";
$path_prefix = "";

// Include database to fetch stats
include "config/database.php";

// Fetch simple dashboard metrics
$posts_count = 0;
$users_count = 0;

$res_posts = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM posts");
if ($res_posts) {
    $row = mysqli_fetch_assoc($res_posts);
    $posts_count = $row['cnt'];
}

$res_users = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users");
if ($res_users) {
    $row = mysqli_fetch_assoc($res_users);
    $users_count = $row['cnt'];
}

include "config/header.php";
?>

<div class="hero-card p-4 p-md-5 rounded-4 shadow-lg" style="background: var(--bg-glass); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.6); max-width: 850px; margin: 0 auto;">
    <h1 class="display-4 fw-extrabold mb-3">Welcome to <span style="background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">MyBlog</span></h1>
    <p class="hero-subtitle lead text-secondary mb-4 mx-auto" style="max-width: 650px;">A premium, secure platform to read, write, and share your thoughts. Join our growing community of writers and start publishing today!</p>
    
    <div class="cta-group d-flex flex-wrap justify-content-center gap-3 mb-5">
        <?php if (isset($_SESSION['username'])): ?>
            <a href="posts/create.php" class="btn btn-primary px-4 py-2.5 rounded-2 d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-pen-nib"></i> Write a New Post
            </a>
            <a href="posts/viewposts.php" class="btn btn-secondary px-4 py-2.5 rounded-2 d-inline-flex align-items-center gap-2 text-dark border-secondary-subtle">
                <i class="fa-solid fa-list-ul"></i> Browse Feed
            </a>
        <?php else: ?>
            <a href="auth/register.php" class="btn btn-primary px-4 py-2.5 rounded-2 d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Join the Community
            </a>
            <a href="posts/viewposts.php" class="btn btn-secondary px-4 py-2.5 rounded-2 d-inline-flex align-items-center gap-2 text-dark border-secondary-subtle">
                <i class="fa-solid fa-book-open"></i> Read Articles
            </a>
        <?php endif; ?>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-3 border border-light-subtle bg-white bg-opacity-75 shadow-sm text-center">
                <div class="stat-number fs-1 fw-bold text-primary mb-1"><?php echo number_format($posts_count); ?></div>
                <div class="stat-label text-muted fw-semibold text-uppercase small" style="letter-spacing: 0.5px;">Published Posts</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-3 border border-light-subtle bg-white bg-opacity-75 shadow-sm text-center">
                <div class="stat-number fs-1 fw-bold text-primary mb-1"><?php echo number_format($users_count); ?></div>
                <div class="stat-label text-muted fw-semibold text-uppercase small" style="letter-spacing: 0.5px;">Active Authors</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-3 border border-light-subtle bg-white bg-opacity-75 shadow-sm text-center">
                <div class="stat-number fs-1 fw-bold text-primary mb-1">100%</div>
                <div class="stat-label text-muted fw-semibold text-uppercase small" style="letter-spacing: 0.5px;">Secure &amp; Fast</div>
            </div>
        </div>
    </div>
</div>

<?php
include "config/footer.php";
?>