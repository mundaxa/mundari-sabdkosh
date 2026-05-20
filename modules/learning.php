<?php
$pageTitle = 'Learning Center - Mundari Sabdkosh';
require_once __DIR__ . '/../header.php';
require_once __DIR__ . '/../sidebar.php';

$db = db();
$quizzes = $db->query("SELECT q.*, u.username as creator_name,
                        (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as question_count
                        FROM quizzes q LEFT JOIN users u ON q.created_by = u.id
                        WHERE q.status = 'active'
                        ORDER BY q.difficulty ASC")->fetchAll();
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search learning...">
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
            <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Learning Center</h1>
            <p style="opacity:0.5;margin-top:4px;">Interactive quizzes, flashcards, and lessons to learn Mundari</p>
        </div>

        <div class="grid grid-auto" style="margin-bottom:28px;">
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon"><i class="fas fa-brain"></i></div>
                <h4 class="fc-title">Daily Quiz</h4>
                <p class="fc-desc">Test your Mundari vocabulary with daily quizzes</p>
                <button class="btn btn-primary btn-sm" style="margin-top:12px;" onclick="alert('Quiz started!')">Start Quiz</button>
            </div>
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon"><i class="fas fa-layer-group"></i></div>
                <h4 class="fc-title">Flashcards</h4>
                <p class="fc-desc">Review words using spaced repetition flashcards</p>
                <button class="btn btn-primary btn-sm" style="margin-top:12px;" onclick="alert('Flashcards loaded!')">Study Now</button>
            </div>
            <div class="feature-card" style="border:1px solid var(--border-color);background:var(--bg-card);">
                <div class="fc-icon"><i class="fas fa-chart-line"></i></div>
                <h4 class="fc-title">Progress Tracker</h4>
                <p class="fc-desc">Track your learning progress and streaks</p>
                <button class="btn btn-primary btn-sm" style="margin-top:12px;" onclick="alert('Viewing progress!')">View Progress</button>
            </div>
        </div>

        <h3 style="font-size:18px;font-weight:600;margin-bottom:16px;">Available Quizzes</h3>
        <div class="grid grid-auto">
            <?php if ($quizzes): foreach ($quizzes as $quiz): ?>
            <div class="card">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;">
                    <div>
                        <h4 style="font-size:15px;font-weight:600;"><?php echo escape($quiz['title']); ?></h4>
                        <p style="font-size:12px;opacity:0.5;margin-top:4px;"><?php echo escape($quiz['description']); ?></p>
                    </div>
                    <span class="badge"><?php echo ucfirst($quiz['difficulty']); ?></span>
                </div>
                <div style="display:flex;gap:16px;font-size:12px;opacity:0.5;margin-bottom:12px;">
                    <span><i class="fas fa-list"></i> <?php echo $quiz['question_count']; ?> questions</span>
                    <span><i class="fas fa-clock"></i> <?php echo $quiz['time_limit']; ?>s</span>
                    <span><i class="fas fa-percent"></i> <?php echo $quiz['passing_score']; ?>% to pass</span>
                </div>
                <div style="display:flex;gap:8px;">
                    <button class="btn btn-primary btn-sm" onclick="alert('Starting quiz: <?php echo escape($quiz['title']); ?>')">Start Quiz</button>
                    <button class="btn btn-ghost btn-sm" onclick="alert('Viewing details')">Details</button>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="empty-state" style="grid-column:1/-1;">
                <div class="empty-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="empty-title">No Quizzes Available</div>
                <div class="empty-desc">Quizzes are being created. Check back soon!</div>
            </div>
            <?php endif; ?>
        </div>

        <?php require_once __DIR__ . '/../footer.php'; ?>
    </div>
</div>
</body>
</html>
