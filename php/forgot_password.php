<?php
/**
 * Forgot Password Page
 * Allows users to request a password reset link via email
 */
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/Utils.php';
include_once ROOT_PATH . '/php/class/CSRF.php';
include_once ROOT_PATH . '/php/class/PasswordReset.php';
include_once ROOT_PATH . '/php/class/Mailer.php';

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    // Validate CSRF
    if (!CSRF::validateToken($_POST['csrf_token'] ?? null)) {
        $message = 'Security validation failed. Please try again.';
        $messageType = 'danger';
    } else {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $messageType = 'danger';
        } else {
            // Find user by email
            $userData = $passwordReset->findUserByEmail($email);

            if ($userData) {
                // Create reset token
                $token = $passwordReset->createToken($userData['user_id']);

                if ($token) {
                    // Send email
                    $resetLink = $passwordReset->getResetLink($token);

                    try {
                        $mailer = new Mailer();
                        if ($mailer->sendPasswordResetEmail($userData['email'], $userData['name'], $resetLink)) {
                            $message = 'A password reset link has been sent to your email address. Please check your inbox.';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to send email. Please try again later.';
                            $messageType = 'danger';
                        }
                    } catch (Exception $e) {
                        error_log("Password reset email error: " . $e->getMessage());
                        $message = 'Failed to send email. Please contact support.';
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'An error occurred. Please try again.';
                    $messageType = 'danger';
                }
            } else {
                // Don't reveal if email exists - security best practice
                $message = 'If an account with that email exists, a password reset link has been sent.';
                $messageType = 'info';
            }
        }
    }
    CSRF::regenerateToken();
}

include_once ROOT_PATH . '/php/include/header.php';
echo "<title>Forgot Password - AMS</title>";
include_once ROOT_PATH . '/php/include/content.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Forgot Password</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your
                        password.</p>

                    <form action="" method="post">
                        <?php echo CSRF::getTokenField(); ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="your.email@example.com" required autofocus>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="request_reset" class="btn btn-primary">
                                Send Reset Link
                            </button>
                            <a href="<?php echo SERVER_ROOT; ?>/index.php" class="btn btn-outline-secondary">
                                Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once ROOT_PATH . '/php/include/footer.php';
?>