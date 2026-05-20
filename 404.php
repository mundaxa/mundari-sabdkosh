<?php
$pageTitle = '404 - Page Not Found';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search...">
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
                    <a href="profile.php" class="dropdown-item"><i class="fas fa-user di-icon"></i> Profile</a>
                    <a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt di-icon"></i> Logout</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content">
        <div class="empty-state" style="margin-top:80px;">
            <div class="empty-icon"><i class="fas fa-map-signs"></i></div>
            <div class="empty-title" style="font-size:72px;font-weight:800;letter-spacing:-0.03em;opacity:0.3;">404</div>
            <div class="empty-title" style="font-size:24px;margin-top:8px;">Page Not Found</div>
            <div class="empty-desc">The page you're looking for doesn't exist or has been moved.</div>
            <a href="index.php" class="btn btn-primary btn-lg"><i class="fas fa-home"></i> Go Home</a>
        </div>
    </div>
</div>
</body>
</html>
