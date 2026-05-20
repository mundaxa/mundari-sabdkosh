-- ============================================================
-- MUNDARI SABDKOSH — Database Schema
-- Tribal Dictionary & Knowledge System
-- MySQL 8+
-- ============================================================

CREATE DATABASE IF NOT EXISTS mundari_sabdkosh
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mundari_sabdkosh;

-- ------------------------------------------------------------
-- ROLES
-- ------------------------------------------------------------
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PERMISSIONS
-- ------------------------------------------------------------
CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- ROLE_PERMISSIONS
-- ------------------------------------------------------------
CREATE TABLE role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- USERS
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar VARCHAR(255),
    bio TEXT,
    role_id INT UNSIGNED DEFAULT 1,
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    twofa_secret VARCHAR(255),
    twofa_enabled TINYINT(1) DEFAULT 0,
    reputation INT DEFAULT 0,
    contributions INT DEFAULT 0,
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- USER_SESSIONS
-- ------------------------------------------------------------
CREATE TABLE user_sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    device_name VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PASSWORD_RESETS
-- ------------------------------------------------------------
CREATE TABLE password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- CATEGORIES
-- ------------------------------------------------------------
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7),
    parent_id INT UNSIGNED DEFAULT NULL,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- WORDS (Dictionary)
-- ------------------------------------------------------------
CREATE TABLE words (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    word VARCHAR(200) NOT NULL,
    word_devanagari VARCHAR(200),
    word_ipa VARCHAR(100),
    pronunciation TEXT,
    meaning_en TEXT NOT NULL,
    meaning_hi TEXT,
    meaning_mun TEXT,
    part_of_speech VARCHAR(50),
    usage_example TEXT,
    usage_example_hi TEXT,
    usage_example_mun TEXT,
    etymology TEXT,
    synonyms TEXT,
    antonyms TEXT,
    region VARCHAR(100),
    dialect VARCHAR(100),
    script_type ENUM('devanagari', 'roman', 'both') DEFAULT 'both',
    audio_file VARCHAR(255),
    category_id INT UNSIGNED,
    status ENUM('pending', 'approved', 'rejected', 'archived') DEFAULT 'pending',
    is_word_of_day TINYINT(1) DEFAULT 0,
    word_of_day_date DATE DEFAULT NULL,
    views_count INT DEFAULT 0,
    search_count INT DEFAULT 0,
    submitted_by INT UNSIGNED,
    reviewed_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_word (word),
    INDEX idx_status (status),
    INDEX idx_word_of_day (is_word_of_day, word_of_day_date),
    FULLTEXT INDEX ft_search (word, meaning_en, meaning_hi, meaning_mun)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- WORD_REVISIONS
-- ------------------------------------------------------------
CREATE TABLE word_revisions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    word_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    field_changed VARCHAR(50),
    old_value TEXT,
    new_value TEXT,
    revision_type ENUM('create', 'update', 'approve', 'reject') DEFAULT 'update',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- WORD_RELATIONS
-- ------------------------------------------------------------
CREATE TABLE word_relations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    word_id INT UNSIGNED NOT NULL,
    related_word_id INT UNSIGNED NOT NULL,
    relation_type ENUM('synonym', 'antonym', 'related', 'see_also') DEFAULT 'related',
    FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE,
    FOREIGN KEY (related_word_id) REFERENCES words(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- ARTICLES (Encyclopedia)
-- ------------------------------------------------------------
CREATE TABLE articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT UNSIGNED,
    tags TEXT,
    author_id INT UNSIGNED,
    status ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft',
    is_featured TINYINT(1) DEFAULT 0,
    is_trending TINYINT(1) DEFAULT 0,
    views_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    FULLTEXT INDEX ft_article_search (title, content, excerpt)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- UPLOADS
-- ------------------------------------------------------------
CREATE TABLE uploads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    filesize BIGINT,
    filetype VARCHAR(100),
    mime_type VARCHAR(100),
    media_type ENUM('image', 'audio', 'video', 'document', 'other') DEFAULT 'other',
    alt_text TEXT,
    caption TEXT,
    metadata JSON,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT UNSIGNED,
    download_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- BOOKMARKS
-- ------------------------------------------------------------
CREATE TABLE bookmarks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    bookmarkable_type ENUM('word', 'article', 'upload') NOT NULL,
    bookmarkable_id INT UNSIGNED NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (user_id, bookmarkable_type, bookmarkable_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- NOTIFICATIONS
-- ------------------------------------------------------------
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    link VARCHAR(500),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- COMMENTS
-- ------------------------------------------------------------
CREATE TABLE comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    commentable_type ENUM('word', 'article', 'upload', 'discussion') NOT NULL,
    commentable_id INT UNSIGNED NOT NULL,
    parent_id INT UNSIGNED DEFAULT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- VOTES
-- ------------------------------------------------------------
CREATE TABLE votes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    votable_type ENUM('comment', 'word', 'article') NOT NULL,
    votable_id INT UNSIGNED NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (user_id, votable_type, votable_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DISCUSSIONS
-- ------------------------------------------------------------
CREATE TABLE discussions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED,
    tags TEXT,
    is_pinned TINYINT(1) DEFAULT 0,
    is_locked TINYINT(1) DEFAULT 0,
    status ENUM('open', 'closed', 'archived') DEFAULT 'open',
    views_count INT DEFAULT 0,
    last_activity TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- QUIZZES
-- ------------------------------------------------------------
CREATE TABLE quizzes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    time_limit INT DEFAULT 300,
    passing_score INT DEFAULT 60,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- QUIZ_QUESTIONS
-- ------------------------------------------------------------
CREATE TABLE quiz_questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT UNSIGNED NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255),
    option_d VARCHAR(255),
    correct_option ENUM('a', 'b', 'c', 'd') NOT NULL,
    explanation TEXT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- QUIZ_ATTEMPTS
-- ------------------------------------------------------------
CREATE TABLE quiz_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    quiz_id INT UNSIGNED NOT NULL,
    score INT DEFAULT 0,
    total_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    time_taken INT DEFAULT 0,
    completed TINYINT(1) DEFAULT 0,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- FLASHCARDS
-- ------------------------------------------------------------
CREATE TABLE flashcards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    word_id INT UNSIGNED,
    front_text TEXT NOT NULL,
    back_text TEXT NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    box_number INT DEFAULT 0,
    next_review TIMESTAMP NULL,
    reviewed_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- LEARNING_PROGRESS
-- ------------------------------------------------------------
CREATE TABLE learning_progress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    words_learned INT DEFAULT 0,
    quizzes_taken INT DEFAULT 0,
    quizzes_passed INT DEFAULT 0,
    flashcards_reviewed INT DEFAULT 0,
    streak_days INT DEFAULT 0,
    last_active_date DATE DEFAULT NULL,
    total_time_minutes INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- ACHIEVEMENTS
-- ------------------------------------------------------------
CREATE TABLE achievements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    badge_color VARCHAR(7),
    criteria JSON,
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- USER_ACHIEVEMENTS
-- ------------------------------------------------------------
CREATE TABLE user_achievements (
    user_id INT UNSIGNED NOT NULL,
    achievement_id INT UNSIGNED NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, achievement_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- REPORTS
-- ------------------------------------------------------------
CREATE TABLE reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT UNSIGNED NOT NULL,
    reportable_type ENUM('word', 'article', 'comment', 'discussion', 'user') NOT NULL,
    reportable_id INT UNSIGNED NOT NULL,
    reason VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    resolved_by INT UNSIGNED,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- ACTIVITY_LOGS
-- ------------------------------------------------------------
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    loggable_type VARCHAR(100),
    loggable_id INT UNSIGNED,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_action (user_id, action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- SYSTEM_SETTINGS
-- ------------------------------------------------------------
CREATE TABLE system_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    setting_type ENUM('text', 'number', 'boolean', 'json', 'email') DEFAULT 'text',
    is_public TINYINT(1) DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- MEDIA
-- ------------------------------------------------------------
CREATE TABLE media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('image', 'audio', 'video', 'document') NOT NULL,
    mime_type VARCHAR(100),
    file_size BIGINT,
    duration INT,
    thumbnail VARCHAR(500),
    artist VARCHAR(255),
    album VARCHAR(255),
    lyrics TEXT,
    language VARCHAR(50),
    region VARCHAR(100),
    category_id INT UNSIGNED,
    uploaded_by INT UNSIGNED,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    plays_count INT DEFAULT 0,
    downloads_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- MAP_REGIONS
-- ------------------------------------------------------------
CREATE TABLE map_regions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    region_type ENUM('tribal', 'cultural', 'geographic', 'historical') DEFAULT 'tribal',
    coordinates POINT,
    boundaries POLYGON,
    color VARCHAR(7),
    population INT,
    language VARCHAR(100),
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- CONTACT_MESSAGES
-- ------------------------------------------------------------
CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- SAMPLE DATA INSERTION
-- ------------------------------------------------------------

INSERT INTO roles (name, slug, description) VALUES
('User', 'user', 'Regular platform user'),
('Student', 'student', 'Student researcher'),
('Teacher', 'teacher', 'Educator and language teacher'),
('Contributor', 'contributor', 'Content contributor'),
('Moderator', 'moderator', 'Community moderator'),
('Archivist', 'archivist', 'Digital archivist'),
('Admin', 'admin', 'Platform administrator'),
('Super Admin', 'super-admin', 'Super administrator with full access');

INSERT INTO permissions (name, slug, description) VALUES
('View Dashboard', 'view-dashboard', 'Access to dashboard'),
('Manage Words', 'manage-words', 'CRUD operations on words'),
('Manage Articles', 'manage-articles', 'CRUD operations on articles'),
('Manage Users', 'manage-users', 'User management'),
('Manage Media', 'manage-media', 'Media management'),
('Manage Settings', 'manage-settings', 'System settings management'),
('Moderate Content', 'moderate-content', 'Content moderation'),
('View Reports', 'view-reports', 'View reports and analytics'),
('Manage Reports', 'manage-reports', 'Manage user reports'),
('Upload Files', 'upload-files', 'Upload media files'),
('Access API', 'access-api', 'API access'),
('Manage Backups', 'manage-backups', 'Database and file backups');

INSERT INTO system_settings (setting_key, setting_value, setting_group, setting_type) VALUES
('site_name', 'Mundari Sabdkosh', 'general', 'text'),
('site_tagline', 'Tribal Dictionary & Knowledge System', 'general', 'text'),
('site_description', 'Preserving and digitizing Mundari language, tribal culture, and indigenous knowledge.', 'general', 'text'),
('site_email', 'admin@mundarisabdkosh.org', 'general', 'email'),
('theme_mode', 'dark', 'appearance', 'text'),
('items_per_page', '20', 'general', 'number'),
('allow_registration', '1', 'general', 'boolean'),
('require_email_verification', '1', 'general', 'boolean'),
('maintenance_mode', '0', 'system', 'boolean'),
('default_user_role', '1', 'system', 'number');

-- Insert categories
INSERT INTO categories (name, slug, description, icon, color) VALUES
('Language', 'language', 'Mundari language and linguistics', 'fa-language', '#4f46e5'),
('Culture', 'culture', 'Tribal culture and traditions', 'fa-dharmachakra', '#0891b2'),
('History', 'history', 'Historical records and timelines', 'fa-landmark', '#7c3aed'),
('People', 'people', 'Notable tribal figures and communities', 'fa-users', '#059669'),
('Places', 'places', 'Tribal regions and geography', 'fa-map-marked-alt', '#d97706'),
('Traditions', 'traditions', 'Customs and traditional practices', 'fa-hand-sparkles', '#dc2626'),
('Songs', 'songs', 'Tribal music and songs', 'fa-music', '#db2777'),
('Proverbs', 'proverbs', 'Mundari proverbs and sayings', 'fa-quote-right', '#65a30d'),
('Rituals', 'rituals', 'Rituals and ceremonies', 'fa-hands-praying', '#0d9488'),
('Folklore', 'folklore', 'Folk tales and oral traditions', 'fa-book-open', '#9333ea');

INSERT INTO achievements (name, slug, description, icon, badge_color, points) VALUES
('First Word', 'first-word', 'Added your first word to the dictionary', 'fa-plus-circle', '#4f46e5', 10),
('Contributor Level 1', 'contributor-1', 'Added 10 words', 'fa-star', '#0891b2', 50),
('Contributor Level 2', 'contributor-2', 'Added 50 words', 'fa-star-half-alt', '#7c3aed', 100),
('Scholar', 'scholar', 'Added 100 words', 'fa-graduation-cap', '#059669', 200),
('Editor', 'editor', 'Made 50 edits to existing words', 'fa-edit', '#d97706', 75),
('Word of the Day', 'word-of-day', 'Your word was featured as Word of the Day', 'fa-crown', '#dc2626', 100),
('Quiz Master', 'quiz-master', 'Scored 100% on a quiz', 'fa-brain', '#db2777', 50),
('Streak 7', 'streak-7', '7-day learning streak', 'fa-fire', '#65a30d', 30),
('Streak 30', 'streak-30', '30-day learning streak', 'fa-fire-alt', '#0d9488', 150),
('Media Contributor', 'media-contributor', 'Uploaded 10 media files', 'fa-photo-video', '#9333ea', 75);
