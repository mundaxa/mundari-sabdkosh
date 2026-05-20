<?php
$pageTitle = 'Manage Words - Admin';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

Auth::requireRole(['admin', 'super-admin', 'moderator']);

$db = db();
$words = $db->query("SELECT w.*, u.username, c.name as category_name FROM words w LEFT JOIN users u ON w.submitted_by = u.id LEFT JOIN categories c ON w.category_id = c.id ORDER BY w.created_at DESC")->fetchAll();
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
            <a href="index.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
        <div class="navbar-right">
            <button class="theme-switch"><i class="fas fa-moon"></i></button>
            <a href="../logout.php" class="btn btn-ghost btn-sm">Logout</a>
        </div>
    </nav>

    <div class="page-content">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <div>
                <h1 style="font-size:24px;font-weight:700;">Words</h1>
                <p style="opacity:0.5;margin-top:4px;"><?php echo count($words); ?> total entries</p>
            </div>
        </div>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr><th>Word</th><th>Category</th><th>Submitter</th><th>Status</th><th>Views</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($words as $w): ?>
                    <tr>
                        <td>
                            <strong><?php echo escape($w['word']); ?></strong>
                            <?php if ($w['word_devanagari']): ?>
                            <div style="font-size:11px;opacity:0.5;"><?php echo escape($w['word_devanagari']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo escape($w['category_name'] ?: '-'); ?></td>
                        <td><?php echo escape($w['username'] ?: 'Anonymous'); ?></td>
                        <td><span class="badge badge-<?php echo $w['status'] === 'approved' ? 'success' : ($w['status'] === 'pending' ? 'warning' : 'error'); ?>"><?php echo ucfirst($w['status']); ?></span></td>
                        <td><?php echo $w['views_count']; ?></td>
                        <td style="opacity:0.5;"><?php echo timeAgo($w['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
