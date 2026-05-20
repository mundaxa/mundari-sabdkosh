<?php
$pageTitle = 'Activity Logs - Admin';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

Auth::requireRole(['admin', 'super-admin']);

$db = db();
$logs = $db->query("SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 100")->fetchAll();
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
        <div style="margin-bottom:24px;">
            <h1 style="font-size:24px;font-weight:700;">Activity Logs</h1>
            <p style="opacity:0.5;margin-top:4px;">Recent system activity (last 100 entries)</p>
        </div>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr><th>User</th><th>Action</th><th>Description</th><th>IP</th><th>Time</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo escape($log['username'] ?: 'System'); ?></td>
                        <td><span class="badge"><?php echo escape($log['action']); ?></span></td>
                        <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo escape($log['description']); ?></td>
                        <td style="font-family:monospace;font-size:12px;"><?php echo escape($log['ip_address']); ?></td>
                        <td style="opacity:0.5;"><?php echo timeAgo($log['created_at']); ?></td>
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
