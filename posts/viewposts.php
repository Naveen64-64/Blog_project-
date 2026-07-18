<?php
$page_title = "Posts";
$active_page = "posts";
$path_prefix = "../";

include "../config/database.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$logged_in = isset($_SESSION['user_id']);

// Require login for viewing posts page
if (!$logged_in) {
    header("Location: ../auth/login.php");
    exit();
}

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}

// 1. Build where conditions and params for count query and fetch query
$where_clauses = [];
$params = [];
$types = "";

if ($filter === 'my') {
    $where_clauses[] = "posts.user_id = ?";
    $params[] = $_SESSION['user_id'];
    $types .= "i";
}

if ($search !== '') {
    $search_terms = array_filter(explode(' ', $search));
    if (!empty($search_terms)) {
        $term_clauses = [];
        foreach ($search_terms as $term) {
            $term_clauses[] = "(posts.title LIKE ? OR posts.content LIKE ? OR users.username LIKE ?)";
            $search_like = "%" . $term . "%";
            $params[] = $search_like;
            $params[] = $search_like;
            $params[] = $search_like;
            $types .= "sss";
        }
        $where_clauses[] = "(" . implode(" AND ", $term_clauses) . ")";
    }
}

// 2. Count total matched posts to determine pagination
$count_query = "SELECT COUNT(*) as total FROM posts LEFT JOIN users ON posts.user_id = users.id";
if (!empty($where_clauses)) {
    $count_query .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt_count = mysqli_prepare($conn, $count_query);
if ($stmt_count) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt_count, $types, ...$params);
    }
    mysqli_stmt_execute($stmt_count);
    $res_count = mysqli_stmt_get_result($stmt_count);
    $row_count = mysqli_fetch_assoc($res_count);
    $total_posts = $row_count['total'] ?? 0;
    mysqli_stmt_close($stmt_count);
} else {
    $total_posts = 0;
}

$total_pages = ceil($total_posts / $limit);
if ($total_pages < 1) {
    $total_pages = 1;
}
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $limit;

// 3. Fetch matched posts for the current page
$posts_query = "SELECT posts.*, users.username FROM posts LEFT JOIN users ON posts.user_id = users.id";
if (!empty($where_clauses)) {
    $posts_query .= " WHERE " . implode(" AND ", $where_clauses);
}
$posts_query .= " ORDER BY posts.created_at DESC LIMIT ? OFFSET ?";

$stmt_posts = mysqli_prepare($conn, $posts_query);
if ($stmt_posts) {
    $posts_types = $types . "ii";
    $posts_params = array_merge($params, [$limit, $offset]);
    mysqli_stmt_bind_param($stmt_posts, $posts_types, ...$posts_params);
    mysqli_stmt_execute($stmt_posts);
    $result = mysqli_stmt_get_result($stmt_posts);
} else {
    $result = false;
}

// Helper function to build pagination URLs preserving search/filter state
function get_page_url($page_num, $filter, $search) {
    $params = ['page' => $page_num];
    if ($filter !== 'all') {
        $params['filter'] = $filter;
    }
    if ($search !== '') {
        $params['search'] = $search;
    }
    return 'viewposts.php?' . http_build_query($params);
}

// Helper function to highlight keywords in HTML-escaped text
function highlight_keywords($text, $search) {
    $escaped = htmlspecialchars($text ?? '');
    if ($search === '') {
        return $escaped;
    }
    
    $keywords = array_filter(explode(' ', $search));
    if (empty($keywords)) {
        return $escaped;
    }
    
    // Sort keywords by length descending to prevent shorter matches inside longer matches from breaking tags
    usort($keywords, function($a, $b) {
        return strlen($b) - strlen($a);
    });
    
    foreach ($keywords as $keyword) {
        $keyword = trim($keyword);
        if ($keyword === '') continue;
        
        $quoted = preg_quote(htmlspecialchars($keyword), '/');
        // Match case-insensitively and wrap in <mark> tag
        $escaped = preg_replace('/(' . $quoted . ')/i', '<mark class="highlight-search">$1</mark>', $escaped);
    }
    return $escaped;
}

include "../config/header.php";
?>

<div class="posts-header-section d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <h1 class="h2 fw-bold text-dark m-0"><?php echo $filter === 'my' ? 'My Posts' : 'All Articles'; ?></h1>
        <?php if ($logged_in): ?>
            <a href="create.php" class="btn btn-primary px-3 py-2 rounded-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i> Create Post
            </a>
        <?php endif; ?>
    </div>
    
    <?php if ($logged_in): ?>
        <div class="filter-tabs d-inline-flex bg-white p-1 rounded-pill border border-light-subtle shadow-sm">
            <a href="viewposts.php?filter=all<?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>" class="filter-tab px-4 py-2 text-decoration-none rounded-pill fw-semibold small <?php echo $filter === 'all' ? 'active bg-primary text-white shadow-sm' : 'text-secondary'; ?>">
                <i class="fa-solid fa-globe"></i> All Posts
            </a>
            <a href="viewposts.php?filter=my<?php echo ($search !== '') ? '&search=' . urlencode($search) : ''; ?>" class="filter-tab px-4 py-2 text-decoration-none rounded-pill fw-semibold small <?php echo $filter === 'my' ? 'active bg-primary text-white shadow-sm' : 'text-secondary'; ?>">
                <i class="fa-solid fa-user"></i> My Posts
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Search Bar Form -->
<form method="get" action="viewposts.php" class="mb-4">
    <?php if ($filter !== 'all'): ?>
        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
    <?php endif; ?>
    <div class="input-group shadow-sm rounded-3 overflow-hidden border">
        <span class="input-group-text bg-white border-0 ps-3">
            <i class="fa-solid fa-magnifying-glass text-muted"></i>
        </span>
        <input 
            type="text" 
            name="search" 
            class="form-control border-0 py-2.5 ps-2 shadow-none" 
            placeholder="Search posts by title or content..." 
            value="<?php echo htmlspecialchars($search); ?>"
        >
        <button class="btn btn-primary px-4 fw-semibold" type="submit">Search</button>
        <?php if ($search !== ''): ?>
            <a href="viewposts.php?filter=<?php echo htmlspecialchars($filter); ?>" class="btn btn-outline-secondary d-flex align-items-center justify-content-center fw-semibold">Clear</a>
        <?php endif; ?>
    </div>
</form>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
    <!-- Posts Responsive Card Grid -->
    <div class="row g-4 mb-4">
        <?php while ($post = mysqli_fetch_assoc($result)): ?>
            <?php 
            $is_owner = $logged_in && ($_SESSION['user_id'] == $post['user_id']);
            ?>
            <div class="col-md-6 col-lg-4 d-flex">
                <article class="card h-100 w-100 border-0 shadow-sm hover-lift d-flex flex-column" style="border-radius: var(--radius-md); overflow: hidden; background: var(--bg-glass); backdrop-filter: blur(10px); transition: transform 0.25s ease, box-shadow 0.25s ease;">
                    <div class="card-body p-4 d-flex flex-column flex-grow-1">
                        <h2 class="card-title h5 fw-bold text-dark mb-3 line-clamp-2"><?php echo highlight_keywords($post['title'], $search); ?></h2>
                        <p class="card-text text-secondary mb-4 flex-grow-1" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"><?php echo nl2br(highlight_keywords($post['content'], $search)); ?></p>
                    </div>
                    <div class="card-footer bg-transparent border-top border-light-subtle px-4 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column text-muted" style="font-size: 0.8rem;">
                            <span class="d-flex align-items-center gap-1 fw-semibold text-primary mb-1">
                                <i class="fa-solid fa-feather"></i> 
                                <?php echo $is_owner ? 'You' : highlight_keywords($post['username'] ?? 'Anonymous', $search); ?>
                            </span>
                            <span class="d-flex align-items-center gap-1">
                                <i class="fa-solid fa-calendar-days"></i> 
                                <?php echo date("F j, Y", strtotime($post['created_at'])); ?>
                            </span>
                        </div>
                        
                        <?php if ($filter === 'my' && $is_owner): ?>
                            <div class="d-flex gap-2">
                                <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: var(--radius-sm);" title="Edit Post">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $post['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" 
                                   style="width: 32px; height: 32px; border-radius: var(--radius-sm);" 
                                   title="Delete Post"
                                   onclick="return confirm('Are you sure you want to permanently delete this post?');"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination Controller -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4 mb-5 text-center">
            <ul class="pagination justify-content-center shadow-sm d-inline-flex rounded-3 overflow-hidden border-0" style="margin: 0 auto;">
                <!-- Previous Button -->
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link py-2 px-3 border-0 bg-white text-dark" href="<?php echo ($page <= 1) ? '#' : get_page_url($page - 1, $filter, $search); ?>" aria-label="Previous">
                        <span aria-hidden="true"><i class="fa-solid fa-angle-left"></i> Prev</span>
                    </a>
                </li>
                
                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link py-2 px-3 border-0 <?php echo ($page == $i) ? 'bg-primary text-white fw-bold' : 'bg-white text-dark'; ?>" href="<?php echo get_page_url($i, $filter, $search); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <!-- Next Button -->
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link py-2 px-3 border-0 bg-white text-dark" href="<?php echo ($page >= $total_pages) ? '#' : get_page_url($page + 1, $filter, $search); ?>" aria-label="Next">
                        <span aria-hidden="true">Next <i class="fa-solid fa-angle-right"></i></span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

<?php else: ?>
    <!-- No matched results empty state fallback -->
    <div class="empty-state text-center py-5 px-4 bg-white rounded-4 shadow-sm border border-light-subtle" style="max-width: 500px; margin: 40px auto;">
        <div class="empty-icon text-muted mb-4 fs-1">
            <i class="fa-regular fa-folder-open"></i>
        </div>
        <h3 class="h4 fw-bold text-dark mb-2">No posts found</h3>
        <p class="text-secondary mb-4">
            <?php 
            if ($search !== '') {
                echo "No matching posts found for '" . htmlspecialchars($search) . "'. Try searching for other terms.";
            } else if ($filter === 'my') {
                echo "You haven't written any posts yet. Start sharing your ideas!";
            } else {
                echo "No posts have been published yet. Be the first to share something!";
            }
            ?>
        </p>
        
        <?php if ($logged_in): ?>
            <a href="create.php" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="fa-solid fa-pen-nib"></i> Write First Post
            </a>
        <?php else: ?>
            <a href="../auth/login.php" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="fa-solid fa-right-to-bracket"></i> Login to Post
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
if (isset($stmt_posts)) {
    mysqli_stmt_close($stmt_posts);
}
include "../config/footer.php";
?>
