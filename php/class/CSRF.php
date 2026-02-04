<?php
/**
 * CSRF Protection Utility
 * Provides functions to generate and validate CSRF tokens
 */
class CSRF
{
    private static $tokenName = 'csrf_token';

    /**
     * Generate a new CSRF token and store it in session
     * @return string The generated token
     */
    public static function generateToken(): string
    {
        if (!isset($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::$tokenName];
    }

    /**
     * Validate a CSRF token against the session token
     * @param string $token The token to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateToken(?string $token): bool
    {
        if (!isset($_SESSION[self::$tokenName]) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION[self::$tokenName], $token);
    }

    /**
     * Regenerate the CSRF token (call after successful validation)
     * @return string The new token
     */
    public static function regenerateToken(): string
    {
        unset($_SESSION[self::$tokenName]);
        return self::generateToken();
    }

    /**
     * Get the token name for form fields
     * @return string The token field name
     */
    public static function getTokenName(): string
    {
        return self::$tokenName;
    }

    /**
     * Generate a hidden input field with the CSRF token
     * @return string HTML hidden input
     */
    public static function getTokenField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Validate token from POST request, die with error if invalid
     * @param bool $regenerate Whether to regenerate token after validation
     */
    public static function requireValidToken(bool $regenerate = true): void
    {
        $token = $_POST[self::$tokenName] ?? null;
        if (!self::validateToken($token)) {
            http_response_code(403);
            die('CSRF validation failed. Please refresh the page and try again.');
        }
        if ($regenerate) {
            self::regenerateToken();
        }
    }
}
