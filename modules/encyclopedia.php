<?php
$pageTitle = 'Encyclopedia - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

$db = db();
$articles = $db->query("SELECT a.*, u.username as author_name, c.name as category_name, c.color as category_color
                         FROM articles a
                         LEFT JOIN users u ON a.author_id = u.id
                         LEFT JOIN categories c ON a.category_id = c.id
                         WHERE a.status = 'published'
                         ORDER BY a.published_at DESC")->fetchAll();
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search encyclopedia...">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="navbar-right">
            <button class="theme-switch"><i class="fas fa-moon"></i></button>
            <?php if ($currentUser): ?>
            <a href="../logout.php" class="btn btn-ghost btn-sm">Logout</a>
            <?php else: ?>
            <a href="../login.php" class="btn btn-primary btn-sm">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content">
        <div style="margin-bottom:28px;">
            <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Encyclopedia</h1>
            <p style="opacity:0.5;margin-top:4px;">Comprehensive knowledge base about Mundari language, culture, and heritage</p>
        </div>

        <div class="grid grid-auto">
            <?php if ($articles): foreach ($articles as $article): ?>
            <div class="article-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <?php if ($article['featured_image']): ?>
                <img src="<?php echo escape($article['featured_image']); ?>" alt="" class="ac-image" loading="lazy">
                <?php endif; ?>
                <div class="ac-body">
                    <div class="ac-tags">
                        <?php if ($article['category_name']): ?>
                        <span class="ac-tag" style="background:<?php echo escape($article['category_color']); ?>20;color:<?php echo escape($article['category_color']); ?>;">
                            <?php echo escape($article['category_name']); ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($article['is_featured']): ?>
                        <span class="ac-tag" style="background:rgba(245,158,11,0.1);color:var(--warning);">Featured</span>
                        <?php endif; ?>
                    </div>
                    <h4 class="ac-title"><?php echo escape($article['title']); ?></h4>
                    <p class="ac-excerpt"><?php echo escape(truncate(strip_tags($article['excerpt'] ?: $article['content']), 150)); ?></p>
                    <div class="ac-meta">
                        <span><i class="fas fa-user"></i> <?php echo escape($article['author_name'] ?: 'Anonymous'); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo formatNumber($article['views_count']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo timeAgo($article['published_at']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="empty-state" style="grid-column:1/-1;">
                <div class="empty-icon"><i class="fas fa-globe"></i></div>
                <div class="empty-title">No Articles Yet</div>
                <div class="empty-desc">The encyclopedia is being built. Check back soon!</div>
            </div>
            <?php endif; ?>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
