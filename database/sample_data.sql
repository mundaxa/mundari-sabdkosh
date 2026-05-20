-- ============================================================
-- MUNDARI SABDKOSH — Sample Data
-- ============================================================

USE mundari_sabdkosh;

-- Sample Users (passwords are 'password123' hashed with password_hash)
INSERT INTO users (username, email, password, full_name, bio, role_id, status, email_verified_at, reputation, contributions) VALUES
('admin', 'admin@mundarisabdkosh.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'Platform administrator', 8, 'active', NOW(), 500, 100),
('soma', 'soma@example.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Soma Munda', 'Language researcher and tribal culture enthusiast', 4, 'active', NOW(), 250, 45),
('arjun', 'arjun@example.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Arjun Hansda', 'Mundari language teacher', 3, 'active', NOW(), 180, 30),
('mary', 'mary@example.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mary Soren', 'Tribal folklore collector', 6, 'active', NOW(), 320, 67),
('ravi', 'ravi@example.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ravi Kumar', 'Student researcher', 2, 'active', NOW(), 90, 12);

-- Sample Words
INSERT INTO words (word, word_devanagari, word_ipa, pronunciation, meaning_en, meaning_hi, meaning_mun, part_of_speech, usage_example, usage_example_hi, region, dialect, category_id, status, is_word_of_day, word_of_day_date, views_count, search_count, submitted_by) VALUES
('Johar', 'जोहार', '/dʒoːˈɦaːr/', 'jo-haar', 'Greeting / Salutation / I bow to you', 'नमस्ते / अभिवादन', 'Johar means a respectful greeting in Mundari culture', 'noun', 'Johar! Tum ko dekh ke khushi hui.', 'Johar! आपको देखकर खुशी हुई।', 'Jharkhand', 'Standard Mundari', 1, 'approved', 1, CURDATE(), 1245, 890, 2),
('Ato', 'आटो', '/ˈaːʈoː/', 'aa-to', 'Village / Settlement', 'गाँव', 'Ato means hamlet or village', 'noun', 'Hamar ato bahut sundar hai.', 'हमारा आटो बहुत सुंदर है।', 'Odisha', 'Southern Mundari', 1, 'approved', 0, NULL, 876, 654, 3),
('Baba', 'बाबा', '/ˈbaːbaː/', 'baa-baa', 'Father / Elder / Respected man', 'पिता / बुजुर्ग', 'Baba is used for father or respected elder', 'noun', 'Baba hamen kahani sunate hain.', 'बाबा हमें कहानी सुनाते हैं।', 'All Regions', 'Standard Mundari', 4, 'approved', 0, NULL, 2341, 1876, 2),
('Gamra', 'गामरा', '/ˈɡaːmraː/', 'gaam-ra', 'Festival / Celebration', 'त्योहार / उत्सव', 'Gamra is a traditional harvest festival', 'noun', 'Gamra mein sab log nachte hain.', 'गामरा में सब लोग नाचते हैं।', 'Jharkhand', 'Standard Mundari', 2, 'approved', 0, NULL, 1678, 1234, 4),
('Horo', 'होरो', '/ˈhoːroː/', 'ho-ro', 'Man / Human being (Munda person)', 'मनुष्य / मुंडा व्यक्ति', 'Horo means a Munda person or human', 'noun', 'Horo dharti ke pahle bete hain.', 'होरो धरती के पहले बेटे हैं।', 'All Regions', 'Standard Mundari', 4, 'approved', 0, NULL, 567, 432, 5);

-- Sample Articles
INSERT INTO articles (title, slug, content, excerpt, category_id, author_id, status, is_featured, is_trending, views_count, published_at) VALUES
('The Mundari Language: An Introduction', 'mundari-language-introduction', '<p>The Mundari language is a Munda language of the Austroasiatic language family, spoken by the Munda people in eastern India. It is primarily spoken in the states of Jharkhand, Odisha, West Bengal, and Assam.</p><p>Mundari is written in both Devanagari and Roman scripts, with efforts underway to standardize the orthography. The language is known for its rich oral tradition, including folk tales, songs, and proverbs that have been passed down through generations.</p>', 'An introduction to the Mundari language, its history, and its significance.', 1, 2, 'published', 1, 1, 3456, NOW()),

('Sarhul: The Festival of Flowers', 'sarhul-festival-flowers', '<p>Sarhul is one of the most important festivals of the Munda community, celebrated during the spring season when the sal trees begin to bloom. The festival marks the beginning of the new year and involves worshiping nature and ancestors.</p><p>During Sarhul, the village priest (Pahan) offers flowers to the sacred grove (Sarna), and the community comes together to dance, sing, and feast. The festival symbolizes the deep connection between the Munda people and nature.</p>', 'Sarhul is the spring festival celebrating nature and renewal in Munda tradition.', 2, 4, 'published', 1, 0, 2890, NOW()),

('Munda Tribal Art and Craft', 'munda-tribal-art-craft', '<p>Munda tribal art is characterized by its vibrant colors, geometric patterns, and deep connection to nature. Traditional art forms include wall paintings (Sohrai), floor decorations (Alpana), and bamboo craft.</p><p>Sohrai paintings are traditionally made by women during harvest festivals, using natural colors derived from clay, charcoal, and leaves. These artworks depict animals, trees, and scenes from daily life.</p>', 'Exploring the rich artistic traditions of the Munda tribal community.', 2, 3, 'published', 0, 1, 1567, NOW()),

('Mundari Oral Traditions', 'mundari-oral-traditions', '<p>The Mundari language has a rich tradition of oral literature, including myths, legends, folk tales, and proverbs. These oral traditions serve as repositories of indigenous knowledge, history, and cultural values.</p><p>Storytelling is an important part of Mundari culture, with tales often featuring animals, spirits, and heroes that teach moral lessons and explain natural phenomena.</p>', 'The rich oral traditions and storytelling heritage of the Mundari people.', 10, 4, 'published', 0, 0, 1234, NOW());

-- Sample Discussions
INSERT INTO discussions (title, slug, content, user_id, category_id, status, is_pinned, views_count) VALUES
('How do you say "water" in Mundari?', 'how-to-say-water-mundari', 'I am learning Mundari and want to know the correct word for water in different contexts.', 5, 1, 'open', 0, 234),
('Preserving Mundari in Schools', 'preserving-mundari-schools', 'We need to discuss how to introduce Mundari language teaching in schools across Jharkhand.', 3, 2, 'open', 1, 567),
('Share Your Favorite Mundari Proverb', 'share-favorite-mundari-proverb', 'Let us create a collection of Mundari proverbs with their meanings.', 4, 1, 'open', 0, 445);

-- Sample Quizzes
INSERT INTO quizzes (title, description, difficulty, time_limit, passing_score, created_by) VALUES
('Basic Mundari Greetings', 'Test your knowledge of basic Mundari greetings and salutations', 'beginner', 180, 60, 2),
('Mundari Vocabulary - Nature', 'Words related to nature, plants, and animals in Mundari', 'intermediate', 300, 60, 3),
('Munda Culture & Traditions', 'Quiz about Munda cultural practices and festivals', 'intermediate', 300, 50, 4);

INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option, sort_order) VALUES
(1, 'What is the Mundari word for greeting?', 'Johar', 'Ato', 'Baba', 'Horo', 'a', 1),
(1, 'What does "Johar" mean?', 'Goodbye', 'Greeting', 'Anger', 'Sleep', 'b', 2),
(1, 'How do you say "thank you" in Mundari?', 'Johar Arka', 'Ato Senna', 'Baba Marang', 'Horo Jati', 'a', 3),
(2, 'What is the Mundari word for water?', 'Daka', 'Ato', 'Baba', 'Horo', 'a', 1),
(2, 'What does "Buru" mean in Mundari?', 'River', 'Mountain', 'Tree', 'House', 'b', 2),
(3, 'Which festival marks the Munda new year?', 'Sarhul', 'Sohrai', 'Karma', 'Mage Porob', 'a', 1),
(3, 'What is the sacred grove called in Munda tradition?', 'Sarna', 'Mandir', 'Masjid', 'Gurudwara', 'a', 2);

-- Sample Media
INSERT INTO media (title, description, file_path, file_type, mime_type, file_size, artist, language, region, category_id, uploaded_by, status) VALUES
('Sarhul Festival Song', 'Traditional song sung during the Sarhul festival', 'uploads/audio/sarhul-song.mp3', 'audio', 'audio/mpeg', 5242880, 'Munda Folk Singers', 'Mundari', 'Jharkhand', 7, 4, 'approved'),
('Munda Wedding Ritual', 'Recording of traditional Munda wedding ceremony chants', 'uploads/audio/wedding-chants.mp3', 'audio', 'audio/mpeg', 7340032, 'Village Elders', 'Mundari', 'Odisha', 9, 4, 'approved'),
('Mundari Folk Tale - The Fox and the Crow', 'Animated folk tale in Mundari with subtitles', 'uploads/video/fox-crow.mp4', 'video', 'video/mp4', 15728640, 'Cultural Preservation Team', 'Mundari', 'All Regions', 10, 4, 'approved');

-- Sample Notifications
INSERT INTO notifications (user_id, type, title, message, is_read) VALUES
(2, 'achievement', 'Congratulations!', 'Your word "Johar" has been featured as Word of the Day!', 0),
(3, 'system', 'Welcome to Mundari Sabdkosh', 'Thank you for joining our community of language preservers!', 0),
(4, 'approval', 'Media Approved', 'Your audio upload "Sarhul Festival Song" has been approved.', 0);

-- Sample Activity Logs
INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES
(2, 'word.create', 'Added new word: Johar', '127.0.0.1'),
(4, 'upload.media', 'Uploaded audio: Sarhul Festival Song', '127.0.0.1'),
(3, 'quiz.attempt', 'Completed quiz: Basic Mundari Greetings', '127.0.0.1');
