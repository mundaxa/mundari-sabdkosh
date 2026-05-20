<?php
$pageTitle = 'Contribute - Mundari Sabdkosh';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';

if (!Auth::check()) {
    SessionManager::setFlash('error', 'Please login to contribute.');
    header('Location: login.php');
    exit;
}

$categories = getCategories();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word = trim($_POST['word'] ?? '');
    $meaningEn = trim($_POST['meaning_en'] ?? '');
    if (empty($word) || empty($meaningEn)) {
        $error = 'Word and English meaning are required.';
    } else {
        $db = db();
        $stmt = $db->prepare("INSERT INTO words (word, word_devanagari, meaning_en, meaning_hi, part_of_speech, usage_example, region, category_id, pronunciation, etymology, synonyms, submitted_by, status) VALUES (:word, :deva, :en, :hi, :pos, :example, :region, :cat, :pron, :etym, :syn, :uid, 'pending')");
        $stmt->execute([
            'word' => $word,
            'deva' => $_POST['word_devanagari'] ?? '',
            'en' => $meaningEn,
            'hi' => $_POST['meaning_hi'] ?? '',
            'pos' => $_POST['part_of_speech'] ?? '',
            'example' => $_POST['usage_example'] ?? '',
            'region' => $_POST['region'] ?? '',
            'cat' => $_POST['category_id'] ?: null,
            'pron' => $_POST['pronunciation'] ?? '',
            'etym' => $_POST['etymology'] ?? '',
            'syn' => $_POST['synonyms'] ?? '',
            'uid' => $_SESSION['user_id']
        ]);
        $wordId = $db->lastInsertId();

        Auth::logActivity($_SESSION['user_id'], 'word.create', "Added new word: $word");
        $db->prepare("UPDATE users SET contributions = contributions + 1 WHERE id = :id")->execute(['id' => $_SESSION['user_id']]);
        $success = 'Your word has been submitted for review. Thank you for contributing!';
    }
}
?>
<div class="main-content">
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="sidebar-toggle hamburger-menu"><i class="fas fa-bars"></i></button>
            <a href="dictionary.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="navbar-search">
            <input type="text" placeholder="Search dictionary...">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="navbar-right">
            <button class="theme-switch"><i class="fas fa-moon"></i></button>
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
        </div>
    </nav>

    <div class="page-content">
        <div style="max-width:720px;margin:0 auto;">
            <div style="margin-bottom:28px;">
                <h1 style="font-size:28px;font-weight:700;letter-spacing:-0.02em;">Contribute a Word</h1>
                <p style="opacity:0.5;margin-top:4px;">Help preserve the Mundari language by adding new words to the dictionary.</p>
            </div>

            <?php if ($success): ?>
            <div class="toast" style="margin-bottom:16px;display:flex;border-left:3px solid var(--success);">
                <i class="fas fa-check-circle" style="color:var(--success);"></i>
                <span><?php echo escape($success); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="toast" style="margin-bottom:16px;display:flex;border-left:3px solid var(--error);">
                <i class="fas fa-exclamation-circle" style="color:var(--error);"></i>
                <span><?php echo escape($error); ?></span>
            </div>
            <?php endif; ?>

            <div class="card">
                <form method="POST" action="">
                    <div class="grid grid-2" style="gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Word (Roman) *</label>
                            <input type="text" name="word" class="form-input" placeholder="e.g. Johar" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Word (Devanagari)</label>
                            <input type="text" name="word_devanagari" class="form-input" placeholder="e.g. जोहार">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">English Meaning *</label>
                        <textarea name="meaning_en" class="form-textarea" placeholder="Enter the meaning in English" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hindi Meaning</label>
                        <textarea name="meaning_hi" class="form-textarea" placeholder="हिंदी में अर्थ लिखें"></textarea>
                    </div>

                    <div class="grid grid-2" style="gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Part of Speech</label>
                            <select name="part_of_speech" class="form-select">
                                <option value="">Select...</option>
                                <option>Noun</option>
                                <option>Verb</option>
                                <option>Adjective</option>
                                <option>Adverb</option>
                                <option>Pronoun</option>
                                <option>Preposition</option>
                                <option>Conjunction</option>
                                <option>Interjection</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Select...</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo escape($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-2" style="gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Pronunciation</label>
                            <input type="text" name="pronunciation" class="form-input" placeholder="e.g. jo-haar">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Region</label>
                            <input type="text" name="region" class="form-input" placeholder="e.g. Jharkhand">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Usage Example</label>
                        <textarea name="usage_example" class="form-textarea" placeholder="Example sentence using the word"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Etymology</label>
                        <input type="text" name="etymology" class="form-input" placeholder="Word origin and history">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Synonyms (comma separated)</label>
                        <input type="text" name="synonyms" class="form-input" placeholder="e.g. greeting, salutation">
                    </div>

                    <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border-color);">
                        <a href="dictionary.php" class="btn btn-ghost">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit for Review</button>
                    </div>
                </form>
            </div>
        </div>

        <?php require_once __DIR__ . '/footer.php'; ?>
    </div>
</div>
</body>
</html>
