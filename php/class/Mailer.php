<?php
/**
 * Mailer Class - Email sending utility using PHPMailer
 * Configured for Gmail SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files (if not using Composer)
require_once ROOT_PATH . '/vendor/PHPMailer/src/Exception.php';
require_once ROOT_PATH . '/vendor/PHPMailer/src/PHPMailer.php';
require_once ROOT_PATH . '/vendor/PHPMailer/src/SMTP.php';

class Mailer
{
    private $mail;
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // SMTP Configuration
        $this->mail->isSMTP();
        $this->mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('SMTP_USER') ?: '';
        $this->mail->Password = getenv('SMTP_PASS') ?: '';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = getenv('SMTP_PORT') ?: 587;

        // Default sender
        $this->fromEmail = getenv('SMTP_FROM_EMAIL') ?: getenv('SMTP_USER');
        $this->fromName = getenv('SMTP_FROM_NAME') ?: 'UoJ Attendance System';

        $this->mail->setFrom($this->fromEmail, $this->fromName);
        $this->mail->isHTML(true);
    }

    /**
     * Send password reset email
     * @param string $toEmail Recipient email
     * @param string $toName Recipient name
     * @param string $resetLink Password reset link
     * @return bool Success status
     */
    public function sendPasswordResetEmail(string $toEmail, string $toName, string $resetLink): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);

            $this->mail->Subject = 'Password Reset Request - UoJ Attendance System';
            $this->mail->Body = $this->getPasswordResetTemplate($toName, $resetLink);
            $this->mail->AltBody = "Hello $toName,\n\nYou have requested to reset your password.\n\nClick the following link to reset: $resetLink\n\nThis link expires in 1 hour.\n\nIf you did not request this, please ignore this email.";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Get HTML template for password reset email
     */
    private function getPasswordResetTemplate(string $name, string $link): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .button { display: inline-block; background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .button:hover { background: #0056b3; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 10px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Hello <strong>$name</strong>,</p>
            <p>You have requested to reset your password for the UoJ Attendance Management System.</p>
            <p>Click the button below to reset your password:</p>
            <p style="text-align: center;">
                <a href="$link" class="button">Reset Password</a>
            </p>
            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; background: #eee; padding: 10px; border-radius: 3px;">$link</p>
            <div class="warning">
                <strong>⚠️ Important:</strong> This link will expire in <strong>1 hour</strong>.
            </div>
            <p>If you did not request this password reset, please ignore this email or contact support if you have concerns.</p>
        </div>
        <div class="footer">
            <p>&copy; University of Jaffna - Attendance Management System</p>
            <p>This is an automated message. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Send account activation email
     */
    public function sendActivationEmail(string $toEmail, string $toName, string $username): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);

            $this->mail->Subject = 'Account Activated - UoJ Attendance System';
            $this->mail->Body = "<h2>Welcome, $toName!</h2><p>Your account ($username) has been activated. You can now log in to the Attendance Management System.</p>";
            $this->mail->AltBody = "Welcome, $toName! Your account ($username) has been activated.";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Send low attendance warning email
     */
    public function sendLowAttendanceWarning(string $toEmail, string $toName, string $courseName, float $percentage): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);

            $this->mail->Subject = "Low Attendance Warning - $courseName";
            $this->mail->Body = "<h2>Attendance Warning</h2><p>Dear $toName,</p><p>Your attendance in <strong>$courseName</strong> is currently at <strong>{$percentage}%</strong>, which is below the required 75%.</p><p>Please ensure you attend upcoming classes to avoid academic penalties.</p>";
            $this->mail->AltBody = "Attendance Warning: Your attendance in $courseName is at {$percentage}%.";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
