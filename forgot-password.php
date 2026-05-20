<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        $db = db();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $stmt->execute(['uid' => $user['id'], 'token' => password_hash($token, PASSWORD_DEFAULT)]);
            $message = 'If an account exists with that email, a password reset link has been sent.';
        } else {
            $message = 'If an account exists with that email, a password reset link has been sent.';
        }
    }
}

$pageTitle = 'Forgot Password - Mundari Sabdkosh';
$theme = $_COOKIE['theme'] ?? 'dark';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark.css">
    <link rel="stylesheet" href="assets/css/light.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-logo">म</div>
            <h1>Reset Password</h1>
            <p>Enter your email and we'll send you a reset link</p>
        </div>

        <div class="auth-card">
            <?php if ($message): ?>
            <div class="toast toast-success" style="margin-bottom:16px;display:flex;">
                <i class="fas fa-check-circle"></i>
                <span><?php echo escape($message); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="toast toast-error" style="margin-bottom:16px;display:flex;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo escape($error); ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="your@email.com" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">Send Reset Link</button>
            </form>

            <div style="text-align:center;margin-top:16px;">
                <a href="login.php" style="font-size:13px;color:var(--accent-primary);">Back to Sign In</a>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/theme.js"></script>
</body>
</html>
