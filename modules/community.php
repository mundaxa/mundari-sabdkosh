<?php
$pageTitle = 'Community - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

$db = db();
$discussions = $db->query("SELECT d.*, u.username, u.avatar,
                            (SELECT COUNT(*) FROM comments WHERE commentable_type='discussion' AND commentable_id=d.id) as reply_count
                            FROM discussions d LEFT JOIN users u ON d.user_id = u.id
                            WHERE d.status = 'open'
                            ORDER BY d.is_pinned DESC, d.last_activity DESC")->fetchAll();
$topContributors = getTopContributors(8);
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search community...">
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
                <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Community</h1>
                <p style="opacity:0.5;margin-top:4px;">Connect, discuss, and collaborate with fellow contributors</p>
            </div>
            <?php if ($currentUser): ?>
            <button class="btn btn-primary"><i class="fas fa-plus"></i> New Discussion</button>
            <?php endif; ?>
        </div>

        <div class="grid grid-2">
            <div>
                <div class="card-header">
                    <h3 style="font-size:16px;font-weight:600;">Discussions</h3>
                </div>
                <?php if ($discussions): foreach ($discussions as $disc): ?>
                <div class="card" style="margin-bottom:12px;cursor:pointer;">
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <img src="<?php echo avatar($disc, 36); ?>" alt="" style="width:36px;height:36px;border-radius:50%;flex-shrink:0;">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <h4 style="font-size:14px;font-weight:600;"><?php echo escape($disc['title']); ?></h4>
                                <?php if ($disc['is_pinned']): ?>
                                <span class="badge" style="background:var(--accent-primary);color:#fff;font-size:10px;">Pinned</span>
                                <?php endif; ?>
                            </div>
                            <p style="font-size:12px;opacity:0.6;margin-top:4px;"><?php echo escape(truncate($disc['content'], 120)); ?></p>
                            <div style="display:flex;gap:16px;margin-top:8px;font-size:11px;opacity:0.4;">
                                <span><i class="fas fa-user"></i> <?php echo escape($disc['username']); ?></span>
                                <span><i class="fas fa-comment"></i> <?php echo $disc['reply_count']; ?> replies</span>
                                <span><i class="fas fa-eye"></i> <?php echo formatNumber($disc['views_count']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo timeAgo($disc['created_at']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-comments"></i></div>
                    <div class="empty-title">No Discussions Yet</div>
                    <div class="empty-desc">Start the first conversation!</div>
                </div>
                <?php endif; ?>
            </div>

            <div>
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-header">
                        <h3 style="font-size:15px;font-weight:600;">Top Contributors</h3>
                    </div>
                    <?php foreach ($topContributors as $c): ?>
                    <div class="contributor-card">
                        <img src="<?php echo avatar($c); ?>" alt="" class="cc-avatar">
                        <div class="cc-info">
                            <div class="cc-name"><?php echo escape($c['full_name'] ?: $c['username']); ?></div>
                            <div class="cc-role"><?php echo $c['contributions']; ?> contributions</div>
                        </div>
                        <div class="cc-stats"><?php echo $c['reputation']; ?> pts</div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <h3 style="font-size:15px;font-weight:600;margin-bottom:12px;">Community Stats</h3>
                    <?php
                    $totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
                    $totalDiscuss = $db->query("SELECT COUNT(*) FROM discussions")->fetchColumn();
                    $totalComments = $db->query("SELECT COUNT(*) FROM comments")->fetchColumn();
                    ?>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);">
                            <span style="opacity:0.5;"><i class="fas fa-users" style="width:20px;"></i> Active Members</span>
                            <strong><?php echo $totalUsers; ?></strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border-color);">
                            <span style="opacity:0.5;"><i class="fas fa-comments" style="width:20px;"></i> Discussions</span>
                            <strong><?php echo $totalDiscuss; ?></strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:8px 0;">
                            <span style="opacity:0.5;"><i class="fas fa-reply" style="width:20px;"></i> Comments</span>
                            <strong><?php echo $totalComments; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
