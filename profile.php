<?php
$pageTitle = 'Profile - Mundari Sabdkosh';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';

Auth::requireAuth();

$db = db();
$stmt = $db->prepare("SELECT u.*, r.name as role_name
                      FROM users u JOIN roles r ON u.role_id = r.id
                      WHERE u.id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$userData = $stmt->fetch();

$stmt = $db->prepare("SELECT COUNT(*) FROM words WHERE submitted_by = :uid");
$stmt->execute(['uid' => $_SESSION['user_id']]);
$wordCount = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bookmarks WHERE user_id = :uid");
$stmt->execute(['uid' => $_SESSION['user_id']]);
$bookmarkCount = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT * FROM user_sessions WHERE user_id = :uid AND is_active = 1 ORDER BY last_activity DESC");
$stmt->execute(['uid' => $_SESSION['user_id']]);
$sessions = $stmt->fetchAll();
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
        <div style="max-width:800px;margin:0 auto;">
            <div class="card" style="text-align:center;padding:40px;margin-bottom:24px;">
                <img src="<?php echo avatar($userData, 96); ?>" alt="" style="width:96px;height:96px;border-radius:50%;margin:0 auto 16px;">
                <h1 style="font-size:24px;font-weight:700;"><?php echo escape($userData['full_name'] ?: $userData['username']); ?></h1>
                <p style="opacity:0.5;">@<?php echo escape($userData['username']); ?> &middot; <?php echo escape($userData['role_name']); ?></p>
                <?php if ($userData['bio']): ?>
                <p style="margin-top:12px;max-width:400px;margin-left:auto;margin-right:auto;"><?php echo escape($userData['bio']); ?></p>
                <?php endif; ?>
                <div style="display:flex;gap:32px;justify-content:center;margin-top:24px;">
                    <div>
                        <div style="font-size:24px;font-weight:700;"><?php echo $wordCount; ?></div>
                        <div style="font-size:12px;opacity:0.5;">Contributions</div>
                    </div>
                    <div>
                        <div style="font-size:24px;font-weight:700;"><?php echo $bookmarkCount; ?></div>
                        <div style="font-size:12px;opacity:0.5;">Bookmarks</div>
                    </div>
                    <div>
                        <div style="font-size:24px;font-weight:700;"><?php echo $userData['reputation']; ?></div>
                        <div style="font-size:12px;opacity:0.5;">Reputation</div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:24px;">
                <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Account Details</h3>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);">
                        <span style="opacity:0.5;">Email</span>
                        <span><?php echo escape($userData['email']); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);">
                        <span style="opacity:0.5;">Role</span>
                        <span><?php echo escape($userData['role_name']); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);">
                        <span style="opacity:0.5;">Member Since</span>
                        <span><?php echo formatDate($userData['created_at']); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;">
                        <span style="opacity:0.5;">Last Login</span>
                        <span><?php echo $userData['last_login_at'] ? timeAgo($userData['last_login_at']) : 'N/A'; ?></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Active Sessions</h3>
                <?php if ($sessions): foreach ($sessions as $session): ?>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border-color);">
                    <div style="width:36px;height:36px;border-radius:var(--radius-sm);background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-<?php echo strtolower($session['device_name']) === 'mobile' ? 'mobile-alt' : 'desktop'; ?>"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:500;"><?php echo escape($session['device_name'] ?: 'Unknown Device'); ?></div>
                        <div style="font-size:11px;opacity:0.5;"><?php echo escape($session['ip_address']); ?></div>
                    </div>
                    <span style="font-size:11px;opacity:0.4;"><?php echo timeAgo($session['last_activity']); ?></span>
                </div>
                <?php endforeach; else: ?>
                <div style="text-align:center;padding:24px;opacity:0.5;font-size:13px;">No active sessions</div>
                <?php endif; ?>
            </div>
        </div>

        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>
</div>
</body>
</html>
