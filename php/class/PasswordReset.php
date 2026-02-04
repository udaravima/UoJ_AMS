<?php
/**
 * PasswordReset Class - Handles password reset token generation and validation
 */
class PasswordReset
{
    private $conn;
    private $table = 'uoj_password_reset';
    private $userTable = 'uoj_user';
    private $lecrTable = 'uoj_lecturer';
    private $stdTable = 'uoj_student';
    private $tokenExpiry = 3600; // 1 hour in seconds

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Find user by email address
     * @param string $email Email to search for
     * @return array|false User data or false if not found
     */
    public function findUserByEmail(string $email): array|false
    {
        // Check lecturer table
        $query = "SELECT u.user_id, u.username, l.lecr_name as name, l.lecr_email as email 
                  FROM {$this->userTable} u 
                  INNER JOIN {$this->lecrTable} l ON u.user_id = l.user_id 
                  WHERE l.lecr_email = ? AND u.user_status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        // Check student table
        $query = "SELECT u.user_id, u.username, s.std_fullname as name, s.std_email as email 
                  FROM {$this->userTable} u 
                  INNER JOIN {$this->stdTable} s ON u.user_id = s.user_id 
                  WHERE s.std_email = ? AND u.user_status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    /**
     * Create a password reset token
     * @param int $userId User ID
     * @return string|false Token or false on failure
     */
    public function createToken(int $userId): string|false
    {
        // Invalidate any existing tokens for this user
        $this->invalidateUserTokens($userId);

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + $this->tokenExpiry);

        $query = "INSERT INTO {$this->table} (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iss', $userId, $token, $expiresAt);

        if ($stmt->execute()) {
            return $token;
        }

        return false;
    }

    /**
     * Validate a password reset token
     * @param string $token Token to validate
     * @return array|false User data or false if invalid
     */
    public function validateToken(string $token): array|false
    {
        $query = "SELECT pr.*, u.username 
                  FROM {$this->table} pr 
                  INNER JOIN {$this->userTable} u ON pr.user_id = u.user_id 
                  WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    /**
     * Mark token as used
     * @param string $token Token to mark
     * @return bool Success status
     */
    public function markTokenUsed(string $token): bool
    {
        $query = "UPDATE {$this->table} SET used = 1 WHERE token = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $token);
        return $stmt->execute();
    }

    /**
     * Invalidate all existing tokens for a user
     * @param int $userId User ID
     */
    public function invalidateUserTokens(int $userId): void
    {
        $query = "UPDATE {$this->table} SET used = 1 WHERE user_id = ? AND used = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
    }

    /**
     * Clean up expired tokens (can be called periodically)
     */
    public function cleanupExpiredTokens(): void
    {
        $query = "DELETE FROM {$this->table} WHERE expires_at < NOW() OR used = 1";
        $this->conn->query($query);
    }

    /**
     * Get the password reset link
     * @param string $token Token
     * @return string Full URL
     */
    public function getResetLink(string $token): string
    {
        return SERVER_ROOT . "/php/reset_password.php?token=" . urlencode($token);
    }
}
