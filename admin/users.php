<?php
$pageTitle = 'Manage Users - Admin';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

Auth::requireRole(['admin', 'super-admin']);

$db = db();
$users = $db->query("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC")->fetchAll();
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
            <a href="index.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
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
                    <a href="../logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt di-icon"></i> Logout</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <div>
                <h1 style="font-size:24px;font-weight:700;">Users</h1>
                <p style="opacity:0.5;margin-top:4px;"><?php echo count($users); ?> registered users</p>
            </div>
        </div>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Contributions</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="display:flex;align-items:center;gap:8px;">
                            <img src="<?php echo avatar($u, 32); ?>" alt="" style="width:32px;height:32px;border-radius:50%;">
                            <div>
                                <strong><?php echo escape($u['full_name'] ?: $u['username']); ?></strong>
                                <div style="font-size:11px;opacity:0.5;">@<?php echo escape($u['username']); ?></div>
                            </div>
                        </td>
                        <td><?php echo escape($u['email']); ?></td>
                        <td><span class="badge"><?php echo escape($u['role_name']); ?></span></td>
                        <td><span class="badge badge-<?php echo $u['status'] === 'active' ? 'success' : 'error'; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                        <td><?php echo $u['contributions']; ?></td>
                        <td style="opacity:0.5;"><?php echo timeAgo($u['created_at']); ?></td>
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
