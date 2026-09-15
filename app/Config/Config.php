<?php

namespace App\Config;

class Config {
    private static $instance;
    private $config;
    private $db;

    private function __construct() {
        $this->loadEnv();
        $this->db = new Database();
        $this->loadFromDatabase();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnv() {
        if (file_exists(__DIR__ . '/../../.env')) {
            $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') === false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value, '"\' ');
                    if (!getenv($key)) {
                        putenv("{$key}={$value}");
                    }
                }
            }
        }
    }

    private function loadFromDatabase() {
        try {
            $conn = $this->db->connect();
            $result = $conn->query('SELECT * FROM configurations LIMIT 1');
            
            if ($result && $row = $result->fetch_assoc()) {
                $this->config = $row;
            }
        } catch (\Exception $e) {
            // Banco ainda não inicializado
        }
    }

    public function get($key, $default = null) {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return $default;
    }

    public function set($key, $value) {
        $this->config[$key] = $value;
        return true;
    }

    public function getSiteName() {
        return $this->get('site_name', 'Acervo Digital');
    }

    public function getSiteUrl() {
        return $this->get('site_url', 'https://www.acervodigital.top');
    }

    public function getLogoHeader() {
        return $this->get('logo_header', '/assets/images/logo-header.png');
    }

    public function getLogoFooter() {
        return $this->get('logo_footer', '/assets/images/logo-footer.png');
    }

    public function getPrimaryColor() {
        return $this->get('primary_color', '#FF6B35');
    }

    public function getSecondaryColor() {
        return $this->get('secondary_color', '#004E89');
    }

    public function getAccentColor() {
        return $this->get('accent_color', '#F7B801');
    }

    public function getEmailContact() {
        return $this->get('email_contact', 'contato@acervodigital.top');
    }

    public function getWhatsAppNumber() {
        return $this->get('whatsapp_number', '47933867486');
    }

    public function isMaintenanceMode() {
        return (bool)$this->get('maintenance_mode', false);
    }
}
