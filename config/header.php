<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure path prefix is defined
if (!isset($path_prefix)) {
    $path_prefix = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " - MyBlog" : "MyBlog - Modern Publishing Platform"; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Premium CSS stylesheet -->
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>css/style.css">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $path_prefix; ?>index.php">
                <span class="logo-icon d-inline-flex align-items-center justify-content-center" style="font-size: 20px; color: var(--primary-color); background: rgba(79, 70, 229, 0.1); width: 40px; height: 40px; border-radius: var(--radius-md);"><i class="fa-solid fa-feather-pointed"></i></span>
                <span class="logo-text fs-4 fw-extrabold" style="background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.5px; font-weight: 800;">MyBlog</span>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Global Navbar Search Form -->
                    <form class="d-flex ms-lg-4 my-3 my-lg-0 w-100 w-lg-auto" action="<?php echo $path_prefix; ?>posts/viewposts.php" method="get" style="max-width: 320px;">
                        <div class="input-group input-group-sm rounded-pill overflow-hidden border border-light-subtle shadow-sm w-100">
                            <span class="input-group-text bg-white border-0 ps-3 pe-1">
                                <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 0.85rem;"></i>
                            </span>
                            <input 
                                class="form-control border-0 py-2 ps-1 shadow-none" 
                                type="search" 
                                name="search" 
                                placeholder="Search articles..." 
                                aria-label="Search" 
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                style="font-size: 0.85rem;"
                            >
                        </div>
                    </form>
                <?php endif; ?>

                <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
                    <li class="nav-item w-100 text-center text-lg-start">
                        <a class="nav-link px-3 py-2 rounded-2 <?php echo isset($active_page) && $active_page == 'home' ? 'active fw-bold text-primary bg-light' : 'text-dark'; ?>" href="<?php echo $path_prefix; ?>index.php">
                            <i class="fa-solid fa-house me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item w-100 text-center text-lg-start">
                        <a class="nav-link px-3 py-2 rounded-2 <?php echo isset($active_page) && $active_page == 'posts' ? 'active fw-bold text-primary bg-light' : 'text-dark'; ?>" href="<?php echo $path_prefix; ?>posts/viewposts.php">
                            <i class="fa-solid fa-newspaper me-1"></i> Posts
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item w-100 text-center text-lg-start">
                            <a class="nav-link px-3 py-2 rounded-2 <?php echo isset($active_page) && $active_page == 'create' ? 'active fw-bold text-primary bg-light' : 'text-dark'; ?>" href="<?php echo $path_prefix; ?>posts/create.php">
                                <i class="fa-solid fa-circle-plus me-1"></i> Create Post
                            </a>
                        </li>
                        <li class="nav-item w-100 text-center text-lg-start">
                            <span class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-light text-primary border border-light-subtle fw-semibold">
                                <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                        </li>
                        <li class="nav-item w-100 text-center text-lg-start ms-lg-2">
                            <a href="<?php echo $path_prefix; ?>auth/logout.php" class="btn btn-outline-danger px-3 py-2 rounded-2 w-100 d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item w-100 text-center text-lg-start">
                            <a class="nav-link px-3 py-2 rounded-2 <?php echo isset($active_page) && $active_page == 'login' ? 'active fw-bold text-primary bg-light' : 'text-dark'; ?>" href="<?php echo $path_prefix; ?>auth/login.php">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item w-100 text-center text-lg-start ms-lg-2">
                            <a href="<?php echo $path_prefix; ?>auth/register.php" class="btn btn-primary px-3 py-2 rounded-2 w-100 d-inline-flex align-items-center justify-content-center gap-1 <?php echo isset($active_page) && $active_page == 'register' ? 'active' : ''; ?>">
                                <i class="fa-solid fa-user-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main content wrapper -->
    <main class="main-wrapper">
