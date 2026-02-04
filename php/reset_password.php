<?php
/**
 * Reset Password Page
 * Allows users to set a new password using a valid reset token
 */
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Utils.php';
include_once ROOT_PATH . '/php/class/CSRF.php';
include_once ROOT_PATH . '/php/class/PasswordReset.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$util = new Utils();
$passwordReset = new PasswordReset($db);

// Redirect if already logged in
if ($user->isLoggedIn()) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}

$message = '';
$messageType = '';
$tokenValid = false;
$token = $_GET['token'] ?? $_POST['token'] ?? '';

// Validate token
if (!empty($token)) {
    $tokenData = $passwordReset->validateToken($token);
    if ($tokenData) {
        $tokenValid = true;
    } else {
        $message = 'Invalid or expired reset link. Please request a new one.';
        $messageType = 'danger';
    }
} else {
    $message = 'No reset token provided.';
    $messageType = 'danger';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password']) && $tokenValid) {
    // Validate CSRF
    if (!CSRF::validateToken($_POST['csrf_token'] ?? null)) {
        $message = 'Security validation failed. Please try again.';
        $messageType = 'danger';
    } else {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate password
        if (strlen($password) < 8) {
            $message = 'Password must be at least 8 characters long.';
            $messageType = 'danger';
        } elseif ($password !== $confirmPassword) {
            $message = 'Passwords do not match.';
            $messageType = 'danger';
        } else {
            // Update password
            if ($user->changeUserPassword($tokenData['user_id'], $password)) {
                // Mark token as used
                $passwordReset->markTokenUsed($token);

                $message = 'Your password has been reset successfully. You can now log in with your new password.';
                $messageType = 'success';
                $tokenValid = false; // Hide the form
            } else {
                $message = 'Failed to update password. Please try again.';
                $messageType = 'danger';
            }
        }
    }
    CSRF::regenerateToken();
}

include_once ROOT_PATH . '/php/include/header.php';
echo "<title>Reset Password - AMS</title>";
include_once ROOT_PATH . '/php/include/content.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Reset Password</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($tokenValid): ?>
                        <p class="text-muted mb-4">Enter your new password below.</p>

                        <form action="" method="post">
                            <?php echo CSRF::getTokenField(); ?>
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" minlength="8"
                                    required autofocus autocomplete="new-password">
                                <div class="form-text">Minimum 8 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                    minlength="8" required autocomplete="new-password">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="reset_password" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="d-grid gap-2">
                            <a href="<?php echo SERVER_ROOT; ?>/php/forgot_password.php" class="btn btn-primary">
                                Request New Reset Link
                            </a>
                            <a href="<?php echo SERVER_ROOT; ?>/index.php" class="btn btn-outline-secondary">
                                Back to Login
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Password match validation
    document.getElementById('confirm_password')?.addEventListener('input', function () {
        const password = document.getElementById('password').value;
        const confirm = this.value;

        if (password !== confirm) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>

<?php
include_once ROOT_PATH . '/php/include/footer.php';
?>