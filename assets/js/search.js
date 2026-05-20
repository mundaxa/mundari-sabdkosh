/* ============================================================
   MUNDARI SABDKOSH — Search System
   ============================================================ */

const SearchSystem = {
    debounceTimer: null,
    searchUrl: 'api/search.php',
    minChars: 2,

    init() {
        this.initHeroSearch();
        this.initNavbarSearch();
        this.initVoiceSearch();
    },

    initHeroSearch() {
        const input = document.querySelector('.hero-search input');
        const suggestions = document.querySelector('.hero-search-suggestions');
        if (!input) return;

        input.addEventListener('input', () => {
            clearTimeout(this.debounceTimer);
            const query = input.value.trim();
            if (query.length >= this.minChars) {
                this.debounceTimer = setTimeout(() => this.fetchSuggestions(query, input), 300);
            } else {
                this.clearSuggestions(input);
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = input.value.trim();
                if (query) {
                    window.location.href = `dictionary.php?search=${encodeURIComponent(query)}`;
                }
            }
        });

        if (suggestions) {
            suggestions.querySelectorAll('.hero-suggestion-tag').forEach(tag => {
                tag.addEventListener('click', () => {
                    window.location.href = `dictionary.php?search=${encodeURIComponent(tag.textContent.trim())}`;
                });
            });
        }
    },

    initNavbarSearch() {
        const input = document.querySelector('.navbar-search input');
        if (!input) return;

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = input.value.trim();
                if (query) {
                    window.location.href = `dictionary.php?search=${encodeURIComponent(query)}`;
                }
            }
        });

        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            setTimeout(() => this.parentElement.classList.remove('focused'), 200);
        });
    },

    initVoiceSearch() {
        const voiceBtns = document.querySelectorAll('.voice-search-btn');
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            voiceBtns.forEach(btn => btn.style.display = 'none');
            return;
        }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        voiceBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const recognition = new SpeechRecognition();
                recognition.lang = 'hi-IN';
                recognition.continuous = false;
                recognition.interimResults = false;

                this.innerHTML = '<i class="fas fa-circle"></i>';
                this.classList.add('listening');

                recognition.onresult = (e) => {
                    const transcript = e.results[0][0].transcript;
                    const searchInput = btn.closest('.hero-search')?.querySelector('input')
                        || document.querySelector('.navbar-search input');
                    if (searchInput) {
                        searchInput.value = transcript;
                        window.location.href = `dictionary.php?search=${encodeURIComponent(transcript)}`;
                    }
                };

                recognition.onerror = () => {
                    this.innerHTML = '<i class="fas fa-microphone"></i>';
                    this.classList.remove('listening');
                };

                recognition.onend = () => {
                    this.innerHTML = '<i class="fas fa-microphone"></i>';
                    this.classList.remove('listening');
                };

                recognition.start();
            });
        });
    },

    fetchSuggestions(query, inputElement) {
        fetch(`${this.searchUrl}?q=${encodeURIComponent(query)}&format=json`)
            .then(response => response.json())
            .then(data => {
                this.renderSuggestions(data, inputElement);
            })
            .catch(() => {
                this.clearSuggestions(inputElement);
            });
    },

    renderSuggestions(results, inputElement) {
        let container = inputElement.parentElement.querySelector('.search-suggestions');
        if (!container) {
            container = document.createElement('div');
            container.className = 'search-suggestions';
            inputElement.parentElement.appendChild(container);
        }

        if (!results || results.length === 0) {
            container.innerHTML = `<div class="ss-empty">No results found</div>`;
            container.style.display = 'block';
            return;
        }

        container.innerHTML = results.slice(0, 8).map(r => `
            <a href="word.php?id=${r.id}" class="ss-item">
                <div class="ss-left">
                    <span class="ss-word">${r.word}</span>
                    <span class="ss-meaning">${r.meaning_en}</span>
                </div>
                <span class="ss-category" style="color:${r.category_color}">${r.category_name || ''}</span>
            </a>
        `).join('');

        container.innerHTML += `
            <a href="dictionary.php?search=${encodeURIComponent(inputElement.value)}" class="ss-all">
                View all results <i class="fas fa-arrow-right"></i>
            </a>
        `;

        container.style.display = 'block';
    },

    clearSuggestions(inputElement) {
        const container = inputElement.parentElement.querySelector('.search-suggestions');
        if (container) container.style.display = 'none';
    }
};

document.addEventListener('DOMContentLoaded', () => SearchSystem.init());
