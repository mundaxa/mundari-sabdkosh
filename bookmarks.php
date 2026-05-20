<?php
$pageTitle = 'Bookmarks - Mundari Sabdkosh';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';

Auth::requireAuth();

$db = db();
$bookmarks = $db->prepare("SELECT b.*,
                           CASE
                               WHEN b.bookmarkable_type = 'word' THEN (SELECT word FROM words WHERE id = b.bookmarkable_id)
                               WHEN b.bookmarkable_type = 'article' THEN (SELECT title FROM articles WHERE id = b.bookmarkable_id)
                               ELSE 'Unknown'
                           END as title,
                           CASE
                               WHEN b.bookmarkable_type = 'word' THEN (SELECT meaning_en FROM words WHERE id = b.bookmarkable_id)
                               ELSE ''
                           END as excerpt
                           FROM bookmarks b
                           WHERE b.user_id = :uid
                           ORDER BY b.created_at DESC");
$bookmarks->execute(['uid' => $_SESSION['user_id']]);
$items = $bookmarks->fetchAll();
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
            <a href="index.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Home</a>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search...">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="navbar-right">
            <button class="theme-switch"><i class="fas fa-moon"></i></button>
            <a href="logout.php" class="btn btn-ghost btn-sm">Logout</a>
        </div>
    </nav>

    <div class="page-content">
        <div class="card-header" style="margin-bottom:24px;">
            <div>
                <h3 class="card-title" style="font-size:24px;">Bookmarks</h3>
                <p class="card-subtitle">Your saved words and articles</p>
            </div>
        </div>

        <?php if ($items): ?>
        <div class="grid grid-auto">
            <?php foreach ($items as $item): ?>
            <div class="card">
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:var(--radius-sm);background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-<?php echo $item['bookmarkable_type'] === 'word' ? 'book' : 'newspaper'; ?>" style="opacity:0.5;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:15px;font-weight:600;"><?php echo escape($item['title']); ?></div>
                        <div style="font-size:12px;opacity:0.5;margin-top:4px;"><?php echo escape(truncate($item['excerpt'], 80)); ?></div>
                        <div style="font-size:11px;opacity:0.3;margin-top:6px;">Bookmarked <?php echo timeAgo($item['created_at']); ?></div>
                    </div>
                    <span class="badge"><?php echo ucfirst($item['bookmarkable_type']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-bookmark"></i></div>
            <div class="empty-title">No Bookmarks Yet</div>
            <div class="empty-desc">Save words and articles to access them quickly later.</div>
            <a href="dictionary.php" class="btn btn-primary">Browse Dictionary</a>
        </div>
        <?php endif; ?>

        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>
</div>
</body>
</html>
