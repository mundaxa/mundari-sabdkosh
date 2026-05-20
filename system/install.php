<?php
$pageTitle = 'Installation - Mundari Sabdkosh';
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0a0b14; color: #e8eaed; max-width: 800px; margin: 40px auto; padding: 24px; line-height: 1.6; }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        h2 { font-size: 20px; margin: 24px 0 12px; }
        .step { background: #151720; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .step-number { display: inline-flex; width: 28px; height: 28px; background: #4f7eff; border-radius: 50%; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 8px; }
        code { background: #1e2030; padding: 2px 8px; border-radius: 4px; font-size: 13px; }
        pre { background: #1e2030; padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
        .success { color: #22c55e; }
        .error { color: #ef4444; }
        .btn { display: inline-block; padding: 10px 20px; background: #4f7eff; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 500; margin-top: 12px; }
        hr { border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 24px 0; }
    </style>
</head>
<body>
    <h1>Mundari Sabdkosh — Installation Guide</h1>
    <p>Follow these steps to set up the platform on your server.</p>
    <hr>

    <div class="step">
        <h2><span class="step-number">1</span> Database Setup</h2>
        <p>Create a MySQL database and import the schema:</p>
        <pre>mysql -u root -p</pre>
        <pre>SOURCE /path/to/mundari-sabdkosh/database/schema.sql;</pre>
        <p class="success">✓ Creates database <code>mundari_sabdkosh</code> with all tables and sample data</p>
    </div>

    <div class="step">
        <h2><span class="step-number">2</span> Configuration</h2>
        <p>Edit <code>includes/config.php</code> with your database credentials:</p>
        <pre>define('DB_HOST', 'localhost');
define('DB_NAME', 'mundari_sabdkosh');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your MySQL password</pre>
    </div>

    <div class="step">
        <h2><span class="step-number">3</span> File Permissions</h2>
        <p>Set proper permissions for upload directories:</p>
        <pre>chmod -R 755 assets/uploads/
chmod -R 755 assets/audio/
chmod -R 755 assets/video/</pre>
    </div>

    <div class="step">
        <h2><span class="step-number">4</span> Web Server</h2>
        <p>Copy the project to your web server directory:</p>
        <pre># XAMPP (Windows)
C:\xampp\htdocs\mundari-sabdkosh\

# Linux
/var/www/html/mundari-sabdkosh/

# Or simply place in your web root</pre>
        <p>Access the platform at: <code>http://localhost/mundari-sabdkosh/</code></p>
    </div>

    <div class="step">
        <h2><span class="step-number">5</span> Default Login</h2>
        <p>Use these credentials to access the admin panel:</p>
        <pre>Email:    admin@mundarisabdkosh.org
Password: password123</pre>
    </div>

    <hr>
    <h2>System Requirements</h2>
    <ul>
        <li>PHP 8.0+</li>
        <li>MySQL 8.0+</li>
        <li>Apache with mod_rewrite (or compatible web server)</li>
        <li>PDO PHP Extension</li>
        <li>GD Library (for image handling)</li>
        <li>mbstring PHP Extension</li>
    </ul>

    <a href="../index.php" class="btn">Go to Platform</a>
</body>
</html>
