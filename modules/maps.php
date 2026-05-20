<?php
$pageTitle = 'Maps & Regions - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search regions...">
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
            <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Maps & Regions</h1>
            <p style="opacity:0.5;margin-top:4px;">Explore tribal regions and cultural zones of Mundari-speaking areas</p>
        </div>

        <div class="card" style="margin-bottom:28px;">
            <div class="interactive-map" style="min-height:400px;"
                 data-regions='[{"name":"Jharkhand","x":0.55,"y":0.3,"r":50,"color":"#4f7eff","label":"Jharkhand - Heartland"},
                               {"name":"Odisha","x":0.48,"y":0.55,"r":45,"color":"#7c3aed","label":"Odisha"},
                               {"name":"West Bengal","x":0.72,"y":0.25,"r":35,"color":"#06b6d4","label":"West Bengal"},
                               {"name":"Assam","x":0.82,"y":0.1,"r":30,"color":"#22c55e","label":"Assam"},
                               {"name":"Bihar","x":0.48,"y":0.12,"r":30,"color":"#f59e0b","label":"Bihar"},
                               {"name":"Chhattisgarh","x":0.32,"y":0.48,"r":35,"color":"#ef4444","label":"Chhattisgarh"}]'>
            </div>
        </div>

        <div class="grid grid-auto">
            <?php
            $regions = [
                ['name' => 'Jharkhand', 'desc' => 'Primary homeland of the Munda people', 'color' => '#4f7eff', 'icon' => 'fa-map-pin'],
                ['name' => 'Odisha', 'desc' => 'Southern Mundari-speaking region', 'color' => '#7c3aed', 'icon' => 'fa-map-pin'],
                ['name' => 'West Bengal', 'desc' => 'Eastern tribal communities', 'color' => '#06b6d4', 'icon' => 'fa-map-pin'],
                ['name' => 'Assam', 'desc' => 'Northeastern Munda settlements', 'color' => '#22c55e', 'icon' => 'fa-map-pin'],
                ['name' => 'Bihar', 'desc' => 'Historical Munda presence', 'color' => '#f59e0b', 'icon' => 'fa-map-pin'],
                ['name' => 'Chhattisgarh', 'desc' => 'Western tribal region', 'color' => '#ef4444', 'icon' => 'fa-map-pin'],
            ];
            foreach ($regions as $r):
            ?>
            <div class="category-card" style="border:1px solid var(--border-color);background:var(--bg-card);text-align:left;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:var(--radius-md);background:<?php echo $r['color']; ?>20;display:flex;align-items:center;justify-content:center;color:<?php echo $r['color']; ?>;">
                        <i class="fas <?php echo $r['icon']; ?>"></i>
                    </div>
                    <div>
                        <div class="cat-name" style="text-align:left;"><?php echo $r['name']; ?></div>
                        <div class="cat-count" style="text-align:left;"><?php echo $r['desc']; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
