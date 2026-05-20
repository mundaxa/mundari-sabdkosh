# Mundari Sabdkosh

**Tribal Dictionary & Knowledge System**

A comprehensive digital platform for preserving and promoting the Mundari language, tribal culture, and indigenous knowledge of the Munda community.

## Features

### Dictionary
- Mundari ↔ English, Hindi bidirectional search
- Devanagari and Roman script support
- Pronunciation guides and audio
- Usage examples and etymology
- Fuzzy search with smart suggestions
- Voice search support

### Knowledge System
- Encyclopedia articles
- Cultural heritage documentation
- Digital archive for manuscripts
- Interactive tribal region maps
- Media library (audio/video/images)

### Learning Tools
- Interactive quizzes
- Flashcards with spaced repetition
- Daily word feature
- Progress tracking
- Achievement system

### Community
- User contributions and moderation
- Discussion forums
- Contributor leaderboards
- Reputation and badges
- Activity feeds

### Administration
- User management with role-based access
- Content moderation workflow
- Analytics dashboard
- Upload management
- System monitoring

## Tech Stack

- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** PHP 8.0+
- **Database:** MySQL 8.0+
- **Server:** Apache / XAMPP compatible

## Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache web server with mod_rewrite
- Composer (optional for package management)

### Quick Start

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/mundari-sabdkosh.git
   ```

2. **Create the database:**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. **Configure:**
   Edit `includes/config.php` with your database credentials.

4. **Set permissions:**
   ```bash
   chmod -R 755 assets/uploads assets/audio assets/video
   ```

5. **Access the platform:**
   Navigate to `http://localhost/mundari-sabdkosh/`

### XAMPP Setup

1. Copy the project folder to `C:\xampp\htdocs\mundari-sabdkosh\`
2. Start Apache and MySQL from XAMPP Control Panel
3. Open phpMyAdmin and import `database/schema.sql`
4. Visit `http://localhost/mundari-sabdkosh/`

### Default Login

- **Email:** admin@mundarisabdkosh.org
- **Password:** password123

## Project Structure

```
mundari-sabdkosh/
├── index.php                 # Homepage
├── login.php                 # Authentication
├── register.php              # Registration
├── dictionary.php            # Dictionary browser
├── word.php                  # Word detail page
├── contribute.php            # Word contribution form
├── profile.php               # User profile
├── bookmarks.php             # User bookmarks
├── 404.php                   # Error page
├── header.php                # Global header
├── sidebar.php               # Navigation sidebar
├── footer.php                # Global footer
├── .htaccess                 # Apache configuration
├── manifest.json             # PWA manifest
├── service-worker.js         # Service worker
│
├── assets/
│   ├── css/
│   │   ├── style.css         # Main styles
│   │   ├── dark.css          # Dark theme
│   │   ├── light.css         # Light theme
│   │   └── responsive.css    # Responsive design
│   ├── js/
│   │   ├── main.js           # Core functionality
│   │   ├── theme.js          # Theme switching
│   │   ├── search.js         # Search system
│   │   └── animations.js     # UI animations
│   ├── images/
│   ├── uploads/
│   ├── audio/
│   └── video/
│
├── includes/
│   ├── config.php            # Configuration
│   ├── db.php                # Database connection
│   ├── auth.php              # Authentication system
│   ├── functions.php         # Utility functions
│   └── session.php           # Session management
│
├── admin/
│   ├── index.php             # Admin dashboard
│   ├── users.php             # User management
│   ├── words.php             # Word management
│   └── logs.php              # Activity logs
│
├── api/
│   ├── search.php            # Search endpoint
│   ├── words.php             # Words API
│   ├── auth.php              # Auth API
│   └── upload.php            # File upload API
│
├── modules/
│   ├── encyclopedia.php      # Knowledge base
│   ├── culture.php           # Culture & heritage
│   ├── learning.php          # Learning center
│   ├── media.php             # Media library
│   ├── maps.php              # Interactive maps
│   ├── community.php         # Community portal
│   └── archive.php           # Digital archive
│
├── database/
│   ├── schema.sql            # Database schema
│   └── sample_data.sql       # Sample data
│
└── system/
    ├── install.php           # Installation guide
    └── health.php            # System health check
```

## Dark & Light Themes

The platform features a sophisticated dual-theme system:
- **Dark mode** (default) — Optimized for reduced eye strain
- **Light mode** — Traditional bright interface
- **Auto-detection** — Follows system preferences
- **Persistent** — Theme choice is saved across sessions

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `api/search.php?q=query` | GET | Search words |
| `api/words.php?action=list` | GET | List words |
| `api/words.php?action=trending` | GET | Trending words |
| `api/words.php?action=word_of_day` | GET | Word of the day |
| `api/auth.php?action=login` | POST | User login |
| `api/auth.php?action=register` | POST | User registration |
| `api/upload.php` | POST | File upload |

## Security Features

- SQL injection prevention (PDO prepared statements)
- XSS protection (output escaping)
- CSRF protection (token validation)
- Password hashing (bcrypt, cost 12)
- Session management
- Input sanitization
- File upload validation
- Login attempt tracking

## Performance

- Lazy loading for images
- Optimized database queries
- CSS/JS minification ready
- Cache headers for static assets
- Gzip compression support
- PWA offline support

## Accessibility

- WCAG 2.1 compliant
- Keyboard navigation support
- Screen reader optimized
- Proper ARIA labels
- High contrast support
- Focus indicators

## License

MIT License

Copyright (c) 2024 Mundari Sabdkosh

## Support

For support, feature requests, or bug reports, please open an issue on the repository or contact the development team.
