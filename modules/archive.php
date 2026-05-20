<?php
$pageTitle = 'Digital Archive - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search archive...">
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
            <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Digital Archive</h1>
            <p style="opacity:0.5;margin-top:4px;">Preserved manuscripts, historical documents, and oral history recordings</p>
        </div>

        <div class="grid grid-auto">
            <div class="card">
                <div class="fc-icon" style="background:rgba(124,58,237,0.15);color:#7c3aed;"><i class="fas fa-scroll"></i></div>
                <h3 style="font-size:16px;font-weight:600;margin:12px 0 6px;">Manuscripts</h3>
                <p style="font-size:13px;opacity:0.6;">Digitized historical manuscripts and documents in Mundari and related languages.</p>
                <span class="badge" style="margin-top:8px;">42 items</span>
            </div>
            <div class="card">
                <div class="fc-icon" style="background:rgba(6,182,212,0.15);color:#06b6d4;"><i class="fas fa-microphone-alt"></i></div>
                <h3 style="font-size:16px;font-weight:600;margin:12px 0 6px;">Oral History</h3>
                <p style="font-size:13px;opacity:0.6;">Recorded interviews and oral traditions passed down through generations.</p>
                <span class="badge" style="margin-top:8px;">28 recordings</span>
            </div>
            <div class="card">
                <div class="fc-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;"><i class="fas fa-landmark"></i></div>
                <h3 style="font-size:16px;font-weight:600;margin:12px 0 6px;">Historical Records</h3>
                <p style="font-size:13px;opacity:0.6;">Colonial-era documents, ethnographic records, and historical accounts.</p>
                <span class="badge" style="margin-top:8px;">156 items</span>
            </div>
            <div class="card">
                <div class="fc-icon" style="background:rgba(239,68,68,0.15);color:#ef4444;"><i class="fas fa-photo-video"></i></div>
                <h3 style="font-size:16px;font-weight:600;margin:12px 0 6px;">Photo Collection</h3>
                <p style="font-size:13px;opacity:0.6;">Historical photographs documenting tribal life and cultural practices.</p>
                <span class="badge" style="margin-top:8px;">89 photos</span>
            </div>
        </div>

        <div class="card" style="margin-top:24px;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">Timeline</h3>
            <?php
            $timeline = [
                ['year' => '2024', 'event' => 'Mundari Sabdkosh platform launched'],
                ['year' => '2023', 'event' => 'Digital preservation initiative started'],
                ['year' => '2020', 'event' => 'Community archiving program begins'],
                ['year' => '2018', 'event' => 'Mundari language documentation project'],
                ['year' => '2015', 'event' => 'First digital dictionary prototype'],
                ['year' => '2000', 'event' => 'Early linguistic research and word collection'],
            ];
            foreach ($timeline as $t):
            ?>
            <div style="display:flex;align-items:flex-start;gap:16px;padding:12px 0;border-bottom:1px solid var(--border-color);">
                <span class="badge" style="min-width:56px;text-align:center;"><?php echo $t['year']; ?></span>
                <span style="font-size:13px;"><?php echo $t['event']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
