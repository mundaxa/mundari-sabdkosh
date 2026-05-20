<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

if (Auth::check()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $result = Auth::login($email, $password, $remember);
        if ($result === true) {
            header('Location: index.php');
            exit;
        } elseif ($result === 'unverified') {
            $error = 'Please verify your email address before logging in.';
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Sign In - Mundari Sabdkosh';
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
            <h1>Welcome Back</h1>
            <p>Sign in to continue to Mundari Sabdkosh</p>
        </div>

        <div class="auth-card">
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
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <div class="form-group" style="display:flex;align-items:center;justify-content:space-between;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                        <input type="checkbox" name="remember" style="width:16px;height:16px;"> Remember me
                    </label>
                    <a href="forgot-password.php" style="font-size:13px;color:var(--accent-primary);">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">Sign In</button>
            </form>

            <div class="auth-divider">or continue with</div>

            <div class="grid grid-2" style="gap:8px;">
                <button class="btn btn-secondary" style="justify-content:center;"><i class="fab fa-google"></i> Google</button>
                <button class="btn btn-secondary" style="justify-content:center;"><i class="fab fa-github"></i> GitHub</button>
            </div>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="register.php" style="color:var(--accent-primary);font-weight:500;">Create one</a>
        </div>
    </div>
</div>
<script src="assets/js/theme.js"></script>
</body>
</html>
