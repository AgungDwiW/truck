<?php
declare(strict_types=1);

final class CSRF
{
    private const SESSION_KEY = '_csrf_tokens';
    private const INPUT_NAME = 'csrf_tokens';
    private const TOKEN_BYTES = 32;
    private static $ttl = 1800;
    private static $maxTokens = 10;

    public static function initSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    public static function configure(int $ttlSeconds = 1800, int $maxTokens = 10)
    {
        self::$ttl = $ttlSeconds;
        self::$maxTokens = $maxTokens;
    }

    public static function generate(string $key): string
    {
        self::initSession();

        $token = bin2hex(random_bytes(self::TOKEN_BYTES));
        $now = time();

        if (!isset($_SESSION[self::SESSION_KEY][$key])) {
            $_SESSION[self::SESSION_KEY][$key] = [];
        }

        $_SESSION[self::SESSION_KEY][$key][$token] = $now + self::$ttl;

        if (count($_SESSION[self::SESSION_KEY][$key]) > self::$maxTokens) {
            asort($_SESSION[self::SESSION_KEY][$key]);
            array_shift($_SESSION[self::SESSION_KEY][$key]);
        }

        return $token;
    }

    // Removed ?string nullable type hint for PHP 7.0 compatibility
    public static function validate(string $key, $token): bool
    {
        self::initSession();

        if (
            empty($token) ||
            empty($_SESSION[self::SESSION_KEY][$key][$token])
        ) {
            return false;
        }

        $expires = $_SESSION[self::SESSION_KEY][$key][$token];

        unset($_SESSION[self::SESSION_KEY][$key][$token]);

        return $expires >= time();
    }

    public static function createInput()
    {
        global $controller; 
        
        $token = self::generate((string)$controller);
        $token = htmlspecialchars($token);
        
        echo "<input type='hidden' name='" . self::INPUT_NAME . "' value='{$token}'>";
    }

    public static function handleValidation()
    {
        global $controller;
        
        // Using PHP 7 null coalescing operator (??) to safely grab the POST variable
        $postToken = $_POST[self::INPUT_NAME] ?? '';
        
        if ($postToken !== '' && self::validate((string)$controller, $postToken)) {
            self::cleanup();
        } else {
            http_response_code(403);
            self::cleanup();
            exit('Invalid CSRF token');
        }
    }

    public static function cleanup()
    {
        self::initSession();
        
        $now = time();

        foreach ($_SESSION[self::SESSION_KEY] as $formKey => $tokens) {
            foreach ($tokens as $token => $expires) {
                if ($expires < $now) {
                    unset($_SESSION[self::SESSION_KEY][$formKey][$token]);
                }
            }

            if (empty($_SESSION[self::SESSION_KEY][$formKey])) {
                unset($_SESSION[self::SESSION_KEY][$formKey]);
            }
        }
    }
}
?>