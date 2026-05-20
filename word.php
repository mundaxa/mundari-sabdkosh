<?php
$id = intval($_GET['id'] ?? 0);
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';

$word = null;
if ($id) {
    $db = db();
    $stmt = $db->prepare("SELECT w.*, c.name as category_name, c.color as category_color,
                          u.username as submitter_name, u.id as submitter_id
                          FROM words w
                          LEFT JOIN categories c ON w.category_id = c.id
                          LEFT JOIN users u ON w.submitted_by = u.id
                          WHERE w.id = :id");
    $stmt->execute(['id' => $id]);
    $word = $stmt->fetch();
    if ($word) {
        $db->prepare("UPDATE words SET views_count = views_count + 1 WHERE id = :id")->execute(['id' => $id]);
        $related = $db->prepare("SELECT w.id, w.word, w.meaning_en FROM word_relations wr JOIN words w ON wr.related_word_id = w.id WHERE wr.word_id = :id AND w.status = 'approved' LIMIT 5");
        $related->execute(['id' => $id]);
        $relatedWords = $related->fetchAll();
    }
}
$trendingWords = getTrendingWords(5);
$pageTitle = ($word ? escape($word['word']) : 'Word Not Found') . ' - Mundari Sabdkosh';
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
            <a href="dictionary.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search dictionary..." aria-label="Search">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="navbar-right">
            <button class="nav-icon-btn voice-search-btn"><i class="fas fa-microphone"></i></button>
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
            <?php else: ?>
            <a href="login.php" class="btn btn-primary btn-sm">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content">
        <?php if ($word): ?>
        <div class="word-detail-grid">
            <div>
                <div class="word-detail-main">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
                        <div>
                            <h1 style="font-size:36px;font-weight:700;letter-spacing:-0.02em;"><?php echo escape($word['word']); ?></h1>
                            <?php if ($word['word_devanagari']): ?>
                            <div style="font-size:22px;opacity:0.5;margin-top:4px;"><?php echo escape($word['word_devanagari']); ?></div>
                            <?php endif; ?>
                            <div style="display:flex;align-items:center;gap:12px;margin-top:10px;flex-wrap:wrap;">
                                <?php if ($word['pronunciation']): ?>
                                <span class="badge badge-info"><i class="fas fa-microphone"></i> <?php echo escape($word['pronunciation']); ?></span>
                                <?php endif; ?>
                                <?php if ($word['word_ipa']): ?>
                                <span class="badge">/<?php echo escape($word['word_ipa']); ?>/</span>
                                <?php endif; ?>
                                <?php if ($word['part_of_speech']): ?>
                                <span class="badge" style="background:var(--accent-primary);color:#fff;"><?php echo escape($word['part_of_speech']); ?></span>
                                <?php endif; ?>
                                <?php if ($word['category_name']): ?>
                                <span class="badge" style="background:<?php echo escape($word['category_color']); ?>20;color:<?php echo escape($word['category_color']); ?>;">
                                    <i class="fas fa-tag"></i> <?php echo escape($word['category_name']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;flex-shrink:0;">
                            <?php if ($word['audio_file']): ?>
                            <button class="btn btn-secondary btn-icon" data-audio="<?php echo escape($word['audio_file']); ?>" onclick="new Audio(this.dataset.audio).play()"><i class="fas fa-volume-up"></i></button>
                            <?php endif; ?>
                            <?php if ($currentUser): ?>
                            <button class="btn btn-secondary btn-icon" onclick="alert('Bookmarked!')"><i class="fas fa-bookmark"></i></button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="meaning-section">
                        <h3>Meaning (English)</h3>
                        <div class="meaning-text"><?php echo nl2br(escape($word['meaning_en'])); ?></div>
                    </div>

                    <?php if ($word['meaning_hi']): ?>
                    <div class="meaning-section">
                        <h3>Meaning (Hindi)</h3>
                        <div class="meaning-text"><?php echo nl2br(escape($word['meaning_hi'])); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($word['meaning_mun']): ?>
                    <div class="meaning-section">
                        <h3>Meaning (Mundari)</h3>
                        <div class="meaning-text"><?php echo nl2br(escape($word['meaning_mun'])); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($word['usage_example']): ?>
                    <div class="meaning-section">
                        <h3>Usage Example</h3>
                        <div style="position:relative;padding:16px;background:var(--bg-tertiary);border-radius:var(--radius-md);font-style:italic;margin-top:4px;">
                            <i class="fas fa-quote-left" style="position:absolute;top:12px;left:12px;opacity:0.2;font-size:16px;"></i>
                            <p style="padding-left:24px;"><?php echo escape($word['usage_example']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($word['etymology']): ?>
                    <div class="meaning-section">
                        <h3>Etymology</h3>
                        <div class="meaning-text"><?php echo escape($word['etymology']); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($word['synonyms'] || $word['antonyms']): ?>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:24px;padding-top:24px;border-top:1px solid var(--border-color);">
                        <?php if ($word['synonyms']): ?>
                        <div>
                            <h3 style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;opacity:0.5;margin-bottom:8px;">Synonyms</h3>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                <?php foreach (explode(',', $word['synonyms']) as $syn): ?>
                                <span class="badge badge-success"><?php echo escape(trim($syn)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($word['antonyms']): ?>
                        <div>
                            <h3 style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;opacity:0.5;margin-bottom:8px;">Antonyms</h3>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                <?php foreach (explode(',', $word['antonyms']) as $ant): ?>
                                <span class="badge badge-error"><?php echo escape(trim($ant)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="sidebar-card">
                    <h4 style="font-size:13px;font-weight:600;margin-bottom:12px;">Word Details</h4>
                    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid var(--border-color);">
                            <span style="opacity:0.5;">Region</span>
                            <span><?php echo escape($word['region'] ?: 'All Regions'); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid var(--border-color);">
                            <span style="opacity:0.5;">Dialect</span>
                            <span><?php echo escape($word['dialect'] ?: 'Standard'); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid var(--border-color);">
                            <span style="opacity:0.5;">Script</span>
                            <span><?php echo ucfirst($word['script_type']); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid var(--border-color);">
                            <span style="opacity:0.5;">Views</span>
                            <span><?php echo formatNumber($word['views_count']); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding-bottom:8px;border-bottom:1px solid var(--border-color);">
                            <span style="opacity:0.5;">Submitted by</span>
                            <span><?php echo escape($word['submitter_name'] ?: 'Anonymous'); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="opacity:0.5;">Added</span>
                            <span><?php echo formatDate($word['created_at']); ?></span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($relatedWords)): ?>
                <div class="sidebar-card">
                    <h4 style="font-size:13px;font-weight:600;margin-bottom:12px;">Related Words</h4>
                    <?php foreach ($relatedWords as $rw): ?>
                    <a href="word.php?id=<?php echo $rw['id']; ?>" style="display:block;padding:8px 0;border-bottom:1px solid var(--border-color);">
                        <div style="font-size:14px;font-weight:500;"><?php echo escape($rw['word']); ?></div>
                        <div style="font-size:12px;opacity:0.5;"><?php echo escape(truncate($rw['meaning_en'], 60)); ?></div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="sidebar-card">
                    <h4 style="font-size:13px;font-weight:600;margin-bottom:12px;">Trending Words</h4>
                    <?php foreach ($trendingWords as $tw): ?>
                    <a href="word.php?id=<?php echo $tw['id']; ?>" style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--border-color);font-size:13px;">
                        <i class="fas fa-fire" style="color:var(--warning);font-size:12px;"></i>
                        <span style="font-weight:500;"><?php echo escape($tw['word']); ?></span>
                        <span style="margin-left:auto;opacity:0.4;font-size:11px;"><?php echo formatNumber($tw['views_count']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-state" style="margin-top:60px;">
            <div class="empty-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="empty-title">Word Not Found</div>
            <div class="empty-desc">The word you're looking for doesn't exist or hasn't been added yet.</div>
            <a href="dictionary.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Dictionary</a>
        </div>
        <?php endif; ?>

        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>
</div>
</body>
</html>
