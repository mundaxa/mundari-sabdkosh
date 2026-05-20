<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

if (Auth::check()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $result = Auth::register([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $fullName
        ]);
        if ($result === true) {
            $success = 'Registration successful! You can now sign in.';
        } elseif ($result === 'email_exists') {
            $error = 'An account with this email already exists.';
        } elseif ($result === 'username_exists') {
            $error = 'This username is already taken.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}

$pageTitle = 'Create Account - Mundari Sabdkosh';
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
            <h1>Create Account</h1>
            <p>Join the Mundari Sabdkosh community</p>
        </div>

        <div class="auth-card">
            <?php if ($error): ?>
            <div class="toast toast-error" style="margin-bottom:16px;display:flex;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo escape($error); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="toast toast-success" style="margin-bottom:16px;display:flex;">
                <i class="fas fa-check-circle"></i>
                <span><?php echo escape($success); ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name (optional)</label>
                    <input type="text" id="full_name" name="full_name" class="form-input" placeholder="Your full name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="username">Username *</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="Choose a username" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="At least 8 characters" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Repeat your password" required minlength="8">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:flex-start;gap:8px;font-size:12px;cursor:pointer;">
                        <input type="checkbox" required style="width:16px;height:16px;margin-top:2px;">
                        I agree to the <a href="#" style="color:var(--accent-primary);">Terms of Service</a> and <a href="#" style="color:var(--accent-primary);">Privacy Policy</a>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">Create Account</button>
            </form>

            <div class="auth-divider">or continue with</div>

            <div class="grid grid-2" style="gap:8px;">
                <button class="btn btn-secondary" style="justify-content:center;"><i class="fab fa-google"></i> Google</button>
                <button class="btn btn-secondary" style="justify-content:center;"><i class="fab fa-github"></i> GitHub</button>
            </div>
        </div>

        <div class="auth-footer">
            Already have an account? <a href="login.php" style="color:var(--accent-primary);font-weight:500;">Sign in</a>
        </div>
    </div>
</div>
<script src="assets/js/theme.js"></script>
</body>
</html>
