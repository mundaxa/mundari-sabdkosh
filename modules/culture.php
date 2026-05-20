<?php
$pageTitle = 'Culture & Heritage - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search culture...">
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
            <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Culture & Heritage</h1>
            <p style="opacity:0.5;margin-top:4px;">Explore the rich cultural heritage of the Munda tribal community</p>
        </div>

        <div class="grid grid-3" style="margin-bottom:28px;">
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon" style="background:rgba(239,68,68,0.15);color:#ef4444;"><i class="fas fa-hands-praying"></i></div>
                <h4 class="fc-title">Festivals</h4>
                <p class="fc-desc">Sarhul, Sohrai, Karma, Mage Porob and other traditional celebrations</p>
            </div>
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon" style="background:rgba(6,182,212,0.15);color:#06b6d4;"><i class="fas fa-palette"></i></div>
                <h4 class="fc-title">Art & Craft</h4>
                <p class="fc-desc">Sohrai paintings, bamboo craft, and traditional Munda artistry</p>
            </div>
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon" style="background:rgba(124,58,237,0.15);color:#7c3aed;"><i class="fas fa-dharmachakra"></i></div>
                <h4 class="fc-title">Rituals</h4>
                <p class="fc-desc">Traditional ceremonies, worship practices, and sacred groves (Sarna)</p>
            </div>
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;"><i class="fas fa-music"></i></div>
                <h4 class="fc-title">Music & Dance</h4>
                <p class="fc-desc">Traditional folk songs, musical instruments, and community dances</p>
            </div>
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon" style="background:rgba(34,197,94,0.15);color:#22c55e;"><i class="fas fa-utensils"></i></div>
                <h4 class="fc-title">Cuisine</h4>
                <p class="fc-desc">Traditional Munda food, ingredients, and culinary practices</p>
            </div>
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon" style="background:rgba(59,130,246,0.15);color:#3b82f6;"><i class="fas fa-leaf"></i></div>
                <h4 class="fc-title">Nature Worship</h4>
                <p class="fc-desc">Sacred groves, nature spirits, and environmental stewardship</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="font-size:18px;font-weight:600;">Featured: Sarhul Festival</h3>
            </div>
            <div class="grid grid-2" style="gap:24px;">
                <div>
                    <p style="font-size:14px;line-height:1.7;opacity:0.8;">Sarhul is one of the most important festivals of the Munda community, celebrated during the spring season when the Sal trees begin to bloom. The festival marks the beginning of the new year and involves worshiping nature and ancestors.</p>
                    <p style="font-size:14px;line-height:1.7;opacity:0.8;margin-top:12px;">During Sarhul, the village priest (Pahan) offers flowers to the sacred grove (Sarna), and the community comes together to dance, sing, and feast. The festival symbolizes the deep connection between the Munda people and nature.</p>
                    <button class="btn btn-primary btn-sm" style="margin-top:16px;">Read More <i class="fas fa-arrow-right"></i></button>
                </div>
                <div style="min-height:200px;border-radius:var(--radius-md);background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-image" style="font-size:48px;opacity:0.2;"></i>
                </div>
            </div>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
