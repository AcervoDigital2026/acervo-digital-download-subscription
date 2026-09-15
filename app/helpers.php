<?php

if (!function_exists('config')) {
    function config($key = null, $default = null) {
        $config = \App\Config\Config::getInstance();
        if ($key === null) {
            return $config;
        }
        return $config->get($key, $default);
    }
}

if (!function_exists('dd')) {
    function dd(...$args) {
        echo '<pre>';
        foreach ($args as $arg) {
            var_dump($arg);
        }
        echo '</pre>';
        die();
    }
}

if (!function_exists('view')) {
    function view($view, $data = []) {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        return config('site_url') . '/assets/' . $path;
    }
}

if (!function_exists('route')) {
    function route($name, $params = []) {
        $routes = [
            'home' => '/',
            'login' => '/login',
            'register' => '/register',
            'dashboard' => '/dashboard',
            'admin' => '/admin',
            'download' => '/download',
        ];
        return $routes[$name] ?? '/';
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($value, $currency = 'BRL') {
        if ($currency === 'BRL') {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }
        return '$' . number_format($value, 2);
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data) {
        if (is_array($data)) {
            return array_map('sanitize', $data);
        }
        return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('validate')) {
    function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                if (strpos($rule, 'required') !== false) {
                    $errors[$field] = "$field é obrigatório";
                }
            } else {
                if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "$field deve ser um email válido";
                }
                if (strpos($rule, 'min:') !== false) {
                    preg_match('/min:(\d+)/', $rule, $matches);
                    if (strlen($data[$field]) < $matches[1]) {
                        $errors[$field] = "$field deve ter no mínimo {$matches[1]} caracteres";
                    }
                }
            }
        }
        return $errors;
    }
}
