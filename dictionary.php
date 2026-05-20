<?php
$pageTitle = 'Dictionary - Mundari Sabdkosh';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

$categories = getCategories();

if ($search) {
    $allWords = searchWords($search, 1000, 0);
    $totalResults = count($allWords);
    $words = array_slice($allWords, $offset, $perPage);
    $paginate = paginate($totalResults, $page, $perPage);
} else {
    $allWords = getWords(1000, 0);
    $totalResults = count($allWords);
    $words = array_slice($allWords, $offset, $perPage);
    $paginate = paginate($totalResults, $page, $perPage);
}

$trendingWords = getTrendingWords(6);
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search dictionary..." value="<?php echo escape($search); ?>">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="navbar-right">
            <button class="nav-icon-btn voice-search-btn"><i class="fas fa-microphone"></i></button>
            <button class="nav-icon-btn" data-modal="#notifications-modal"><i class="fas fa-bell"></i></button>
            <button class="theme-switch"><i class="fas fa-moon"></i></button>
            <?php if ($currentUser): ?>
            <div class="dropdown">
                <div class="user-profile" onclick="this.parentElement.classList.toggle('active')">
                    <img src="<?php echo avatar($currentUser); ?>" alt="" class="user-avatar">
                    <span class="user-name"><?php echo escape($currentUser['full_name'] ?: $currentUser['username']); ?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="profile.php" class="dropdown-item"><i class="fas fa-user di-icon"></i> Profile</a>
                    <a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt di-icon"></i> Logout</a>
                </div>
            </div>
            <?php else: ?>
            <a href="login.php" class="btn btn-primary btn-sm">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content">
        <div class="dictionary-header">
            <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Dictionary</h1>
            <p style="opacity:0.5;margin-top:4px;">
                <?php if ($search): ?>
                Showing results for "<strong><?php echo escape($search); ?></strong>"
                (<?php echo $totalResults; ?> entries found)
                <?php else: ?>
                Browse the complete Mundari dictionary (<?php echo $totalResults; ?> entries)
                <?php endif; ?>
            </p>
            <div class="dictionary-filters">
                <span class="filter-chip <?php echo !$category ? 'active' : ''; ?>" onclick="window.location.href='dictionary.php'">All</span>
                <?php foreach ($categories as $cat): ?>
                <span class="filter-chip <?php echo $category === $cat['slug'] ? 'active' : ''; ?>"
                      onclick="window.location.href='dictionary.php?category=<?php echo $cat['slug']; ?>'"
                      style="--chip-color:<?php echo escape($cat['color']); ?>">
                    <i class="fas <?php echo escape($cat['icon'] ?: 'fa-folder'); ?>"></i>
                    <?php echo escape($cat['name']); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="word-detail-grid">
            <div>
                <?php if ($words): foreach ($words as $word): ?>
                <a href="word.php?id=<?php echo $word['id']; ?>" class="word-entry" style="border:1px solid var(--border-color);margin-bottom:8px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="flex:1;min-width:0;">
                            <div class="we-word"><?php echo escape($word['word']); ?></div>
                            <?php if ($word['word_devanagari']): ?>
                            <div class="we-script"><?php echo escape($word['word_devanagari']); ?></div>
                            <?php endif; ?>
                            <div class="we-meaning"><?php echo escape(truncate($word['meaning_en'], 120)); ?></div>
                            <div class="we-meta">
                                <span><i class="fas fa-tag"></i> <?php echo escape($word['category_name'] ?? 'Uncategorized'); ?></span>
                                <span><i class="fas fa-eye"></i> <?php echo formatNumber($word['views_count']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo timeAgo($word['created_at']); ?></span>
                            </div>
                        </div>
                        <?php if ($word['audio_file']): ?>
                        <span style="opacity:0.4;"><i class="fas fa-volume-up"></i></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; else: ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-book-open"></i></div>
                    <div class="empty-title">No words found</div>
                    <div class="empty-desc">
                        <?php if ($search): ?>
                        No results for "<?php echo escape($search); ?>". Try a different search term.
                        <?php else: ?>
                        The dictionary is being populated. Be the first to contribute!
                        <?php endif; ?>
                    </div>
                    <?php if ($search): ?>
                    <a href="dictionary.php" class="btn btn-primary">Browse All</a>
                    <?php else: ?>
                    <a href="contribute.php" class="btn btn-primary">Add a Word</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($paginate && $paginate['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($paginate['has_prev']): ?>
                    <a href="?page=<?php echo $paginate['prev_page']; ?>&search=<?php echo urlencode($search); ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $paginate['total_pages']; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="page-btn <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($paginate['has_next']): ?>
                    <a href="?page=<?php echo $paginate['next_page']; ?>&search=<?php echo urlencode($search); ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div>
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-header">
                        <h4 style="font-size:14px;font-weight:600;">Quick Stats</h4>
                    </div>
                    <?php $stats = [
                        ['label' => 'Total Words', 'value' => $totalResults, 'icon' => 'fa-book'],
                        ['label' => 'Categories', 'value' => count($categories), 'icon' => 'fa-folder'],
                    ]; ?>
                    <?php foreach ($stats as $s): ?>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);">
                        <span style="font-size:13px;"><i class="fas <?php echo $s['icon']; ?>" style="width:20px;opacity:0.5;"></i> <?php echo $s['label']; ?></span>
                        <span style="font-size:14px;font-weight:600;"><?php echo $s['value']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 style="font-size:14px;font-weight:600;">Trending Words</h4>
                    </div>
                    <?php foreach ($trendingWords as $w): ?>
                    <a href="word.php?id=<?php echo $w['id']; ?>" style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--border-color);font-size:13px;">
                        <i class="fas fa-fire" style="color:var(--warning);font-size:12px;"></i>
                        <span style="font-weight:500;"><?php echo escape($w['word']); ?></span>
                        <span style="margin-left:auto;opacity:0.4;font-size:11px;"><?php echo formatNumber($w['views_count']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>
</div>
</body>
</html>
