<?php
$pageTitle = 'Admin Dashboard - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

Auth::requireRole(['admin', 'super-admin']);

$db = db();
$stats = [
    'users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'words' => $db->query("SELECT COUNT(*) FROM words")->fetchColumn(),
    'pending_words' => $db->query("SELECT COUNT(*) FROM words WHERE status='pending'")->fetchColumn(),
    'articles' => $db->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'media' => $db->query("SELECT COUNT(*) FROM media")->fetchColumn(),
    'reports' => $db->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetchColumn(),
    'comments' => $db->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
    'discussions' => $db->query("SELECT COUNT(*) FROM discussions")->fetchColumn(),
];

$recentUsers = $db->query("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC LIMIT 5")->fetchAll();
$recentWords = $db->query("SELECT w.*, u.username FROM words w LEFT JOIN users u ON w.submitted_by = u.id ORDER BY w.created_at DESC LIMIT 5")->fetchAll();
$activities = getRecentActivity(8);
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
            <span style="font-weight:600;">Admin Dashboard</span>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search admin...">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="navbar-right">
            <button class="theme-switch"><i class="fas fa-moon"></i></button>
            <?php if ($currentUser): ?>
            <div class="dropdown">
                <div class="user-profile" onclick="this.parentElement.classList.toggle('active')">
                    <img src="<?php echo avatar($currentUser); ?>" alt="" class="user-avatar">
                    <span class="user-name"><?php echo escape($currentUser['full_name'] ?: $currentUser['username']); ?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="../profile.php" class="dropdown-item"><i class="fas fa-user di-icon"></i> Profile</a>
                    <a href="../logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt di-icon"></i> Logout</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content">
        <h1 style="font-size:24px;font-weight:700;margin-bottom:24px;">Admin Dashboard</h1>

        <div class="admin-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(79,126,255,0.15);color:var(--accent-primary);"><i class="fas fa-users"></i></div>
                <div class="stat-value"><?php echo $stats['users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(124,58,237,0.15);color:var(--accent-secondary);"><i class="fas fa-book"></i></div>
                <div class="stat-value"><?php echo $stats['words']; ?></div>
                <div class="stat-label">Total Words</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:var(--warning);"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-value"><?php echo $stats['pending_words']; ?></div>
                <div class="stat-label">Pending Words</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(6,182,212,0.15);color:var(--accent-tertiary);"><i class="fas fa-newspaper"></i></div>
                <div class="stat-value"><?php echo $stats['articles']; ?></div>
                <div class="stat-label">Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:var(--success);"><i class="fas fa-photo-video"></i></div>
                <div class="stat-value"><?php echo $stats['media']; ?></div>
                <div class="stat-label">Media Files</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:rgba(239,68,68,0.15);color:var(--error);"><i class="fas fa-flag"></i></div>
                <div class="stat-value"><?php echo $stats['reports']; ?></div>
                <div class="stat-label">Pending Reports</div>
            </div>
        </div>

        <div class="grid grid-2" style="margin-bottom:28px;">
            <div class="card">
                <div class="card-header">
                    <h3 style="font-size:15px;font-weight:600;">Recent Users</h3>
                    <a href="users.php" class="card-action">Manage</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr><th>User</th><th>Role</th><th>Status</th><th>Joined</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $u): ?>
                        <tr>
                            <td style="display:flex;align-items:center;gap:8px;">
                                <img src="<?php echo avatar($u, 28); ?>" alt="" style="width:28px;height:28px;border-radius:50%;">
                                <span><?php echo escape($u['full_name'] ?: $u['username']); ?></span>
                            </td>
                            <td><span class="badge"><?php echo escape($u['role_name']); ?></span></td>
                            <td><span class="badge badge-<?php echo $u['status'] === 'active' ? 'success' : 'error'; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                            <td style="opacity:0.5;"><?php echo timeAgo($u['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 style="font-size:15px;font-weight:600;">Recent Words</h3>
                    <a href="words.php" class="card-action">Manage</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr><th>Word</th><th>Submitter</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentWords as $w): ?>
                        <tr>
                            <td><strong><?php echo escape($w['word']); ?></strong></td>
                            <td><?php echo escape($w['username'] ?: 'Anonymous'); ?></td>
                            <td><span class="badge badge-<?php echo $w['status'] === 'approved' ? 'success' : ($w['status'] === 'pending' ? 'warning' : 'error'); ?>"><?php echo ucfirst($w['status']); ?></span></td>
                            <td style="opacity:0.5;"><?php echo timeAgo($w['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="font-size:15px;font-weight:600;">Recent Activity</h3>
            </div>
            <?php foreach ($activities as $a): ?>
            <div class="activity-item">
                <div class="activity-dot"></div>
                <div class="activity-content">
                    <strong><?php echo escape($a['username'] ?? 'System'); ?></strong>
                    <?php echo escape($a['description']); ?>
                    <div class="activity-time"><?php echo timeAgo($a['created_at']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
