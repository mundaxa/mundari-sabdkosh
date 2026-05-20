<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$navItems = [
    'main' => [
        'title' => 'Main',
        'items' => [
            ['label' => 'Home', 'icon' => 'fa-home', 'link' => 'index.php', 'active' => $currentPage === 'index.php'],
        ]
    ],
    'knowledge' => [
        'title' => 'Knowledge',
        'items' => [
            ['label' => 'Dictionary', 'icon' => 'fa-book', 'link' => 'dictionary.php', 'badge' => 'New'],
            ['label' => 'Encyclopedia', 'icon' => 'fa-globe', 'link' => 'modules/encyclopedia.php'],
            ['label' => 'Culture & Heritage', 'icon' => 'fa-dharmachakra', 'link' => 'modules/culture.php'],
            ['label' => 'Archive', 'icon' => 'fa-archive', 'link' => 'modules/archive.php'],
        ]
    ],
    'research' => [
        'title' => 'Research',
        'items' => [
            ['label' => 'Research Portal', 'icon' => 'fa-flask', 'link' => '#'],
            ['label' => 'Learning Center', 'icon' => 'fa-graduation-cap', 'link' => 'modules/learning.php'],
            ['label' => 'Media Library', 'icon' => 'fa-photo-video', 'link' => 'modules/media.php'],
            ['label' => 'Maps & Regions', 'icon' => 'fa-map-marked-alt', 'link' => 'modules/maps.php'],
            ['label' => 'AI Tools', 'icon' => 'fa-robot', 'link' => '#', 'badge' => 'AI'],
        ]
    ],
    'community' => [
        'title' => 'Community',
        'items' => [
            ['label' => 'Community', 'icon' => 'fa-users', 'link' => 'modules/community.php'],
            ['label' => 'Discussions', 'icon' => 'fa-comments', 'link' => '#'],
            ['label' => 'Contributors', 'icon' => 'fa-trophy', 'link' => '#'],
            ['label' => 'Events & Calendar', 'icon' => 'fa-calendar', 'link' => '#'],
        ]
    ],
    'tools' => [
        'title' => 'Tools',
        'items' => [
            ['label' => 'Transliteration', 'icon' => 'fa-exchange-alt', 'link' => '#'],
            ['label' => 'OCR Scanner', 'icon' => 'fa-scanner', 'link' => '#'],
            ['label' => 'Developer Portal', 'icon' => 'fa-code', 'link' => '#'],
            ['label' => 'API Docs', 'icon' => 'fa-plug', 'link' => '#'],
        ]
    ],
    'system' => [
        'title' => 'System',
        'items' => [
            ['label' => 'Bookmarks', 'icon' => 'fa-bookmark', 'link' => 'bookmarks.php'],
            ['label' => 'Reports', 'icon' => 'fa-chart-bar', 'link' => '#'],
            ['label' => 'Admin Panel', 'icon' => 'fa-shield-alt', 'link' => 'admin/index.php', 'badge' => isset($_SESSION['role_slug']) && in_array($_SESSION['role_slug'], ['admin', 'super-admin']) ? '' : null],
            ['label' => 'Settings', 'icon' => 'fa-cog', 'link' => '#'],
        ]
    ]
];

// Filter admin if not admin
if (!isset($_SESSION['role_slug']) || !in_array($_SESSION['role_slug'], ['admin', 'super-admin'])) {
    unset($navItems['system']['items'][array_search('Admin Panel', array_column($navItems['system']['items'], 'label'))]);
}
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="assets/images/birsa-munda.jpg" alt="Birsa Munda" style="width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0;">
        <div class="logo-text">
            Mundari Sabdkosh
            <span class="logo-sub">Tribal Dictionary & Knowledge System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($navItems as $section): ?>
        <div class="nav-section">
            <div class="nav-section-title"><?php echo $section['title']; ?></div>
            <?php foreach ($section['items'] as $item): ?>
            <a href="<?php echo $item['link']; ?>"
               class="nav-item <?php echo !empty($item['active']) ? 'active' : ''; ?>"
               data-tooltip="<?php echo $item['label']; ?>">
                <i class="fas <?php echo $item['icon']; ?> nav-icon"></i>
                <span class="nav-label"><?php echo $item['label']; ?></span>
                <?php if (!empty($item['badge'])): ?>
                <span class="nav-badge"><?php echo $item['badge']; ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="contribute.php" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
            <i class="fas fa-plus"></i>
            <span class="nav-label">Contribute</span>
        </a>
    </div>
</aside>
