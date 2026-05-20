/* ============================================================
   MUNDARI SABDKOSH — Static Data Layer
   Replaces PHP/MySQL for GitHub Pages
   ============================================================ */

const DB = {
    words: [],
    categories: [],
    articles: [],
    users: [],
    discussions: [],
    quizzes: [],
    notifications: [],
    currentUser: null,

    async init() {
        try {
            const [words, cats, arts, users, disc, quiz] = await Promise.all([
                fetch('assets/data/words.json').then(r => r.json()),
                fetch('assets/data/categories.json').then(r => r.json()),
                fetch('assets/data/articles.json').then(r => r.json()),
                fetch('assets/data/users.json').then(r => r.json()),
                fetch('assets/data/discussions.json').then(r => r.json()),
                fetch('assets/data/quizzes.json').then(r => r.json())
            ]);
            this.words = words;
            this.categories = cats;
            this.articles = arts;
            this.users = users;
            this.discussions = disc;
            this.quizzes = quiz;

            const saved = localStorage.getItem('mundari-user');
            if (saved) this.currentUser = JSON.parse(saved);

            return true;
        } catch (e) {
            console.warn('Data load error:', e);
            return false;
        }
    },

    getWord(id) { return this.words.find(w => w.id === parseInt(id)); },
    getCategory(id) { return this.categories.find(c => c.id === parseInt(id)); },
    getUser(id) { return this.users.find(u => u.id === parseInt(id)); },
    getArticle(id) { return this.articles.find(a => a.id === parseInt(id)); },

    searchWords(query) {
        const q = query.toLowerCase();
        return this.words.filter(w =>
            w.status === 'approved' &&
            (w.word.toLowerCase().includes(q) ||
             w.meaning_en.toLowerCase().includes(q) ||
             w.meaning_hi.toLowerCase().includes(q) ||
             (w.word_devanagari || '').includes(q))
        ).sort((a, b) => b.views_count - a.views_count);
    },

    trendingWords(limit = 10) {
        return this.words.filter(w => w.status === 'approved')
            .sort((a, b) => b.views_count - a.views_count).slice(0, limit);
    },

    recentWords(limit = 10) {
        return this.words.filter(w => w.status === 'approved')
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, limit);
    },

    wordOfDay() {
        return this.words.find(w => w.is_word_of_day) || this.words[0];
    },

    featuredArticles() {
        return this.articles.filter(a => a.is_featured);
    },

    topContributors(limit = 10) {
        return this.users.filter(u => u.status === 'active')
            .sort((a, b) => b.contributions - a.contributions).slice(0, limit);
    },

    getCategoryName(id) { const c = this.getCategory(id); return c ? c.name : 'Uncategorized'; },
    getCategoryColor(id) { const c = this.getCategory(id); return c ? c.color : '#666'; },
    getCategoryIcon(id) { const c = this.getCategory(id); return c ? c.icon : 'fa-folder'; },

    login(email, password) {
        const user = this.users.find(u => u.email === email && u.status === 'active');
        if (user && password === 'password123') {
            this.currentUser = user;
            localStorage.setItem('mundari-user', JSON.stringify(user));
            return true;
        }
        return false;
    },

    register(data) {
        if (this.users.find(u => u.email === data.email)) return 'email_exists';
        if (this.users.find(u => u.username === data.username)) return 'username_exists';
        const newUser = {
            id: this.users.length + 1,
            username: data.username,
            full_name: data.full_name || data.username,
            email: data.email,
            avatar: '',
            bio: data.bio || '',
            role_name: 'User',
            role_slug: 'user',
            status: 'active',
            reputation: 0,
            contributions: 0,
            created_at: new Date().toISOString()
        };
        this.users.push(newUser);
        return true;
    },

    logout() {
        this.currentUser = null;
        localStorage.removeItem('mundari-user');
    },

    isLoggedIn() { return this.currentUser !== null; },
    hasRole(slug) { return this.currentUser && this.currentUser.role_slug === slug; },
    isAdmin() { return this.currentUser && ['admin', 'super-admin'].includes(this.currentUser.role_slug); },

    addWord(data) {
        const newWord = {
            id: this.words.length + 1,
            ...data,
            views_count: 0,
            search_count: 0,
            is_word_of_day: false,
            status: 'pending',
            submitted_by: this.currentUser?.id || 0,
            created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
        };
        this.words.unshift(newWord);
        if (this.currentUser) this.currentUser.contributions++;
        return newWord;
    },

    getRelatedWords(wordId, limit = 5) {
        const word = this.getWord(wordId);
        if (!word || !word.synonyms) return [];
        const syns = word.synonyms.split(',').map(s => s.trim().toLowerCase());
        return this.words.filter(w =>
            w.id !== wordId && w.status === 'approved' &&
            w.category_id === word.category_id
        ).slice(0, limit);
    },

    formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toString();
    },

    timeAgo(date) {
        const diff = Math.floor((new Date() - new Date(date)) / 1000);
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
        return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    },

    avatar(user, size = 40) {
        if (user.avatar) return user.avatar;
        const name = user.full_name || user.username || 'U';
        const initial = name.charAt(0).toUpperCase();
        const colors = ['#4f46e5','#0891b2','#7c3aed','#059669','#d97706','#dc2626','#db2777','#65a30d','#0d9488','#9333ea'];
        const color = colors[Math.abs(name.split('').reduce((a,c) => a + c.charCodeAt(0), 0)) % colors.length];
        return `https://ui-avatars.com/api/?name=${initial}&background=${color.replace('#','')}&color=fff&size=${size}`;
    },

    truncate(text, len = 100) {
        return text && text.length > len ? text.substring(0, len) + '...' : (text || '');
    }
};

window.DB = DB;
