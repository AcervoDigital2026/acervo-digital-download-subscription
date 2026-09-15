<?php

namespace App\Security;

class Security {
    
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }

    public static function verifyRecaptcha($token) {
        if (!getenv('RECAPTCHA_ENABLED')) {
            return true;
        }

        // Simples verificação de checkbox (não é segundo passo)
        if (isset($_POST['recaptcha_checkbox']) && $_POST['recaptcha_checkbox'] === 'on') {
            return true;
        }
        return false;
    }

    public static function encryptData($data, $key) {
        $method = 'AES-256-CBC';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
        $encrypted = openssl_encrypt($data, $method, $key, true, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decryptData($data, $key) {
        $method = 'AES-256-CBC';
        $data = base64_decode($data);
        $iv = substr($data, 0, openssl_cipher_iv_length($method));
        $encrypted = substr($data, openssl_cipher_iv_length($method));
        return openssl_decrypt($encrypted, $method, $key, true, $iv);
    }

    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    public static function logSecurityEvent($userId, $action, $status = 'success', $details = []) {
        try {
            $db = new \App\Config\Database();
            $conn = $db->connect();
            
            $details_json = json_encode($details);
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            $stmt = $conn->prepare('INSERT INTO security_logs (user_id, action, ip_address, user_agent, status, details) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('isssss', $userId, $action, $ip, $user_agent, $status, $details_json);
            $stmt->execute();
        } catch (\Exception $e) {
            error_log('Erro ao registrar evento de segurança: ' . $e->getMessage());
        }
    }
}
