<?php
$pageTitle = 'Mundari Sabdkosh - Tribal Dictionary & Knowledge System';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';

$wordOfDay = getWordOfDay();
$trendingWords = getTrendingWords(5);
$recentWords = getWords(8);
$featuredArticles = getArticles(3, 0, true);
$topContributors = getTopContributors(6);
$categories = getCategories();
$totalWords = db()->query("SELECT COUNT(*) FROM words WHERE status='approved'")->fetchColumn();
$totalArticles = db()->query("SELECT COUNT(*) FROM articles WHERE status='published'")->fetchColumn();
$totalUsers = db()->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
$totalMedia = db()->query("SELECT COUNT(*) FROM media WHERE status='approved'")->fetchColumn();
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="navbar-search">
            <input type="text" placeholder="Search words, articles, culture..." aria-label="Search">
            <i class="fas fa-search search-icon"></i>
        </div>

        <div class="navbar-right">
            <button class="nav-icon-btn voice-search-btn" title="Voice Search">
                <i class="fas fa-microphone"></i>
            </button>
            <button class="nav-icon-btn" title="Notifications" data-modal="#notifications-modal">
                <i class="fas fa-bell"></i>
                <?php if ($unreadCount > 0): ?>
                <span class="badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </button>
            <button class="nav-icon-btn" title="Messages">
                <i class="fas fa-envelope"></i>
            </button>
            <button class="theme-switch" title="Toggle Theme">
                <i class="fas fa-moon"></i>
            </button>
            <?php if ($currentUser): ?>
            <div class="dropdown">
                <div class="user-profile" onclick="this.parentElement.classList.toggle('active')">
                    <img src="<?php echo avatar($currentUser); ?>" alt="" class="user-avatar">
                    <span class="user-name"><?php echo escape($currentUser['full_name'] ?: $currentUser['username']); ?></span>
                    <i class="fas fa-chevron-down" style="font-size:10px;opacity:0.5"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="profile.php" class="dropdown-item"><i class="fas fa-user di-icon"></i> Profile</a>
                    <a href="bookmarks.php" class="dropdown-item"><i class="fas fa-bookmark di-icon"></i> Bookmarks</a>
                    <a href="#" class="dropdown-item"><i class="fas fa-cog di-icon"></i> Settings</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt di-icon"></i> Logout</a>
                </div>
            </div>
            <?php else: ?>
            <a href="login.php" class="btn btn-primary btn-sm">Sign In</a>
            <a href="register.php" class="btn btn-ghost btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content">
        <!-- HERO SECTION -->
        <section class="hero-section" data-reveal>
            <canvas id="hero-particles" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>
            <div class="hero-content">
                <h1 class="hero-title">Mundari Sabdkosh</h1>
                <p class="hero-subtitle">Preserving and digitizing the Mundari language, tribal culture, and indigenous knowledge for future generations.</p>
                <div class="hero-search">
                    <input type="text" placeholder="Search any word in Mundari, Hindi, or English..." aria-label="Search dictionary">
                    <button class="hero-search-btn"><i class="fas fa-search"></i></button>
                    <div class="hero-search-suggestions">
                        <span class="hero-suggestion-tag">Johar</span>
                        <span class="hero-suggestion-tag">Ato</span>
                        <span class="hero-suggestion-tag">Baba</span>
                        <span class="hero-suggestion-tag">Sarhul</span>
                        <span class="hero-suggestion-tag">Horo</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ANALYTICS PANELS -->
        <div class="grid grid-4" style="margin-bottom:28px;" data-reveal>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-value" data-count="<?php echo $totalWords; ?>" data-duration="1000">0</div>
                <div class="stat-label">Dictionary Words</div>
                <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 12% this month</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
                <div class="stat-value" data-count="<?php echo $totalArticles; ?>" data-duration="1000">0</div>
                <div class="stat-label">Articles</div>
                <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 8% this month</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value" data-count="<?php echo $totalUsers; ?>" data-duration="1000">0</div>
                <div class="stat-label">Contributors</div>
                <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 15% this month</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-photo-video"></i></div>
                <div class="stat-value" data-count="<?php echo $totalMedia; ?>" data-duration="1000">0</div>
                <div class="stat-label">Media Items</div>
                <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 5% this month</span>
            </div>
        </div>

        <!-- WORD OF THE DAY -->
        <?php if ($wordOfDay): ?>
        <section class="card" style="margin-bottom:28px;" data-reveal>
            <div class="word-of-day">
                <div style="flex:1;min-width:0;">
                    <div class="wod-badge"><i class="fas fa-crown"></i> Word of the Day</div>
                    <h2 class="wod-word"><?php echo escape($wordOfDay['word']); ?></h2>
                    <?php if ($wordOfDay['word_devanagari']): ?>
                    <div class="wod-script"><?php echo escape($wordOfDay['word_devanagari']); ?></div>
                    <?php endif; ?>
                    <div class="wod-pronunciation">
                        <span>Pronunciation: <?php echo escape($wordOfDay['pronunciation'] ?: $wordOfDay['word_ipa'] ?: 'N/A'); ?></span>
                        <?php if ($wordOfDay['audio_file']): ?>
                        <button class="wod-audio-btn" data-audio="<?php echo escape($wordOfDay['audio_file']); ?>">
                            <i class="fas fa-volume-up"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="wod-meaning"><?php echo escape($wordOfDay['meaning_en']); ?></div>
                    <?php if ($wordOfDay['usage_example']): ?>
                    <div class="wod-example">"<?php echo escape($wordOfDay['usage_example']); ?>"</div>
                    <?php endif; ?>
                    <div class="wod-meta">
                        <span><i class="fas fa-tag"></i> <?php echo escape($wordOfDay['part_of_speech'] ?: 'N/A'); ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo escape($wordOfDay['region'] ?: 'All Regions'); ?></span>
                        <span><i class="fas fa-user"></i> <?php echo escape($wordOfDay['submitter_name'] ?: 'Anonymous'); ?></span>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;display:none;">
                    <div style="font-size:48px;opacity:0.1;"><i class="fas fa-quote-right"></i></div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- FEATURE CARDS -->
        <section style="margin-bottom:28px;" data-reveal>
            <div class="card-header">
                <div>
                    <h3 class="card-title">Explore Features</h3>
                    <p class="card-subtitle">Tools to explore and preserve tribal knowledge</p>
                </div>
            </div>
            <div class="grid grid-3">
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-exchange-alt"></i></div>
                    <h4 class="fc-title">AI Transliterator</h4>
                    <p class="fc-desc">Convert between Devanagari and Roman scripts intelligently</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-scanner"></i></div>
                    <h4 class="fc-title">OCR Scanner</h4>
                    <p class="fc-desc">Digitize printed manuscripts and documents with text recognition</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-microphone"></i></div>
                    <h4 class="fc-title">Voice Search</h4>
                    <p class="fc-desc">Search the dictionary using your voice in multiple languages</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-brain"></i></div>
                    <h4 class="fc-title">Daily Quiz</h4>
                    <p class="fc-desc">Test your knowledge with interactive daily language quizzes</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h4 class="fc-title">Learning Mode</h4>
                    <p class="fc-desc">Structured lessons and flashcards for effective language learning</p>
                </div>
                <div class="feature-card">
                    <div class="fc-icon"><i class="fas fa-users"></i></div>
                    <h4 class="fc-title">Community Portal</h4>
                    <p class="fc-desc">Connect with contributors, discuss, and collaborate on preservation</p>
                </div>
            </div>
        </section>

        <!-- CATEGORY GRID -->
        <section style="margin-bottom:28px;" data-reveal>
            <div class="card-header">
                <div>
                    <h3 class="card-title">Browse Categories</h3>
                    <p class="card-subtitle">Explore knowledge across different domains</p>
                </div>
                <a href="dictionary.php" class="card-action">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="grid grid-auto">
                <?php foreach ($categories as $cat): ?>
                <div class="category-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                    <div class="cat-icon" style="color:<?php echo escape($cat['color']); ?>;">
                        <i class="fas <?php echo escape($cat['icon'] ?: 'fa-folder'); ?>"></i>
                    </div>
                    <div class="cat-name"><?php echo escape($cat['name']); ?></div>
                    <div class="cat-count"><?php echo $cat['word_count']; ?> entries</div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- FEATURED ARTICLES & TRENDING WORDS -->
        <div class="grid grid-2" style="margin-bottom:28px;">
            <!-- Featured Articles -->
            <section data-reveal>
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Featured Articles</h3>
                        <p class="card-subtitle">Curated knowledge and insights</p>
                    </div>
                    <a href="modules/encyclopedia.php" class="card-action">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="grid" style="grid-template-columns:1fr;">
                    <?php if ($featuredArticles): foreach ($featuredArticles as $article): ?>
                    <div class="article-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                        <?php if ($article['featured_image']): ?>
                        <img src="<?php echo escape($article['featured_image']); ?>" alt="" class="ac-image" loading="lazy">
                        <?php endif; ?>
                        <div class="ac-body">
                            <div class="ac-tags">
                                <span class="ac-tag">Featured</span>
                                <?php if ($article['is_trending']): ?>
                                <span class="ac-tag" style="background:rgba(245,158,11,0.1);color:var(--warning);">Trending</span>
                                <?php endif; ?>
                            </div>
                            <h4 class="ac-title"><?php echo escape($article['title']); ?></h4>
                            <p class="ac-excerpt"><?php echo escape(truncate(strip_tags($article['excerpt'] ?: $article['content']), 150)); ?></p>
                            <div class="ac-meta">
                                <span><i class="fas fa-user"></i> <?php echo escape($article['author_name'] ?: 'Anonymous'); ?></span>
                                <span><i class="fas fa-eye"></i> <?php echo formatNumber($article['views_count']); ?></span>
                                <span><i class="fas fa-calendar"></i> <?php echo timeAgo($article['published_at']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </section>

            <!-- Trending Words -->
            <section data-reveal>
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Trending Words</h3>
                        <p class="card-subtitle">Most viewed words this week</p>
                    </div>
                    <a href="dictionary.php" class="card-action">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    <?php if ($trendingWords): foreach ($trendingWords as $i => $word): ?>
                    <a href="word.php?id=<?php echo $word['id']; ?>" class="word-entry" style="display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--border-color);">
                        <span style="font-weight:700;font-size:14px;opacity:0.3;min-width:24px;">#<?php echo $i + 1; ?></span>
                        <div style="flex:1;min-width:0;">
                            <div class="we-word"><?php echo escape($word['word']); ?></div>
                            <div class="we-meaning"><?php echo escape(truncate($word['meaning_en'], 60)); ?></div>
                        </div>
                        <span class="badge" style="flex-shrink:0;">
                            <i class="fas fa-eye"></i> <?php echo formatNumber($word['views_count']); ?>
                        </span>
                    </a>
                    <?php endforeach; endif; ?>
                </div>
            </section>
        </div>

        <!-- INTERACTIVE MAP -->
        <section class="card" style="margin-bottom:28px;" data-reveal>
            <div class="card-header">
                <div>
                    <h3 class="card-title">Tribal Regions</h3>
                    <p class="card-subtitle">Interactive map of Mundari-speaking regions</p>
                </div>
            </div>
            <div class="interactive-map" style="min-height:350px;"></div>
        </section>

        <!-- RECENT WORDS & TOP CONTRIBUTORS -->
        <div class="grid grid-2" style="margin-bottom:28px;">
            <section data-reveal>
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Recent Words</h3>
                        <p class="card-subtitle">Latest additions to the dictionary</p>
                    </div>
                    <a href="dictionary.php" class="card-action">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div>
                    <?php if ($recentWords): foreach ($recentWords as $word): ?>
                    <a href="word.php?id=<?php echo $word['id']; ?>" class="word-entry" style="border-bottom:1px solid var(--border-color);">
                        <div class="we-word"><?php echo escape($word['word']); ?></div>
                        <?php if ($word['word_devanagari']): ?>
                        <div class="we-script"><?php echo escape($word['word_devanagari']); ?></div>
                        <?php endif; ?>
                        <div class="we-meaning"><?php echo escape(truncate($word['meaning_en'], 80)); ?></div>
                        <div class="we-meta">
                            <span><i class="fas fa-tag"></i> <?php echo escape($word['category_name'] ?: 'Uncategorized'); ?></span>
                            <span><i class="fas fa-clock"></i> <?php echo timeAgo($word['created_at']); ?></span>
                        </div>
                    </a>
                    <?php endforeach; endif; ?>
                </div>
            </section>

            <section data-reveal>
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Top Contributors</h3>
                        <p class="card-subtitle">Leading community members</p>
                    </div>
                </div>
                <div>
                    <?php if ($topContributors): foreach ($topContributors as $contributor): ?>
                    <div class="contributor-card">
                        <img src="<?php echo avatar($contributor); ?>" alt="" class="cc-avatar" loading="lazy">
                        <div class="cc-info">
                            <div class="cc-name"><?php echo escape($contributor['full_name'] ?: $contributor['username']); ?></div>
                            <div class="cc-role"><?php echo escape($contributor['role_name']); ?></div>
                        </div>
                        <div class="cc-stats"><?php echo $contributor['contributions']; ?> contributions</div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </section>
        </div>

        <!-- ACTIVITY FEED -->
        <section class="card" style="margin-bottom:28px;" data-reveal>
            <div class="card-header">
                <div>
                    <h3 class="card-title">Recent Activity</h3>
                    <p class="card-subtitle">Latest actions from the community</p>
                </div>
            </div>
            <div>
                <?php $activities = getRecentActivity(5); ?>
                <?php if ($activities): foreach ($activities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-content">
                        <strong><?php echo escape($activity['username'] ?? 'System'); ?></strong>
                        <?php echo escape($activity['description']); ?>
                        <div class="activity-time"><?php echo timeAgo($activity['created_at']); ?></div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </section>

        <!-- QUICK ACTIONS -->
        <section style="margin-bottom:28px;" data-reveal>
            <div class="card-header">
                <div>
                    <h3 class="card-title">Quick Actions</h3>
                    <p class="card-subtitle">Frequently used tools and features</p>
                </div>
            </div>
            <div class="grid grid-auto">
                <a href="contribute.php" class="btn btn-secondary" style="padding:16px 24px;"><i class="fas fa-plus-circle"></i> Add New Word</a>
                <a href="#" class="btn btn-secondary" style="padding:16px 24px;"><i class="fas fa-upload"></i> Upload Media</a>
                <a href="modules/learning.php" class="btn btn-secondary" style="padding:16px 24px;"><i class="fas fa-graduation-cap"></i> Start Learning</a>
                <a href="modules/community.php" class="btn btn-secondary" style="padding:16px 24px;"><i class="fas fa-comments"></i> Join Discussion</a>
            </div>
        </section>

        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>
</div>

<!-- Notifications Modal -->
<div class="modal-overlay" id="notifications-modal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Notifications</h3>
            <button class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div>
            <?php if ($currentUser): ?>
                <?php $notifications = getNotifications($currentUser['id'], 5); ?>
                <?php if ($notifications): foreach ($notifications as $n): ?>
                <div class="activity-item">
                    <div class="activity-dot" style="<?php echo $n['is_read'] ? 'opacity:0.3;' : ''; ?>"></div>
                    <div class="activity-content">
                        <strong><?php echo escape($n['title']); ?></strong>
                        <?php echo escape($n['message']); ?>
                        <div class="activity-time"><?php echo timeAgo($n['created_at']); ?></div>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-bell"></i></div>
                    <div class="empty-title">No notifications</div>
                    <div class="empty-desc">You're all caught up!</div>
                </div>
                <?php endif; ?>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-bell"></i></div>
                <div class="empty-title">Sign in to view notifications</div>
                <a href="login.php" class="btn btn-primary">Sign In</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
