<?php
$pageTitle = 'Media Library - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

$db = db();
$mediaItems = $db->query("SELECT m.*, u.username as uploader_name, c.name as category_name
                          FROM media m
                          LEFT JOIN users u ON m.uploaded_by = u.id
                          LEFT JOIN categories c ON m.category_id = c.id
                          WHERE m.status = 'approved'
                          ORDER BY m.created_at DESC")->fetchAll();
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search media...">
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
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
            <div>
                <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Media Library</h1>
                <p style="opacity:0.5;margin-top:4px;">Audio, video, and image archives of tribal heritage</p>
            </div>
            <?php if ($currentUser): ?>
            <button class="btn btn-primary"><i class="fas fa-upload"></i> Upload Media</button>
            <?php endif; ?>
        </div>

        <div class="grid grid-auto">
            <?php if ($mediaItems): foreach ($mediaItems as $media): ?>
            <div class="card" style="cursor:pointer;">
                <div style="width:100%;height:180px;border-radius:var(--radius-md);background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                    <i class="fas fa-<?php echo $media['file_type'] === 'audio' ? 'music' : ($media['file_type'] === 'video' ? 'video' : 'image'); ?>" style="font-size:36px;opacity:0.3;"></i>
                </div>
                <h4 style="font-size:14px;font-weight:600;"><?php echo escape($media['title']); ?></h4>
                <p style="font-size:12px;opacity:0.5;margin-top:4px;"><?php echo escape(truncate($media['description'], 80)); ?></p>
                <div style="display:flex;gap:12px;margin-top:8px;font-size:11px;opacity:0.4;">
                    <span><i class="fas fa-user"></i> <?php echo escape($media['uploader_name'] ?: 'Anonymous'); ?></span>
                    <span><i class="fas fa-play"></i> <?php echo formatNumber($media['plays_count']); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo escape($media['category_name'] ?: 'Uncategorized'); ?></span>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="empty-state" style="grid-column:1/-1;">
                <div class="empty-icon"><i class="fas fa-photo-video"></i></div>
                <div class="empty-title">Media Library Empty</div>
                <div class="empty-desc">No media files have been uploaded yet. Be the first to contribute!</div>
            </div>
            <?php endif; ?>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
