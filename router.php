<?php
// router.php

class Router {
    private static $routes = [];
    private static $sessionStarted = false;

    public static function init() {
        if (!self::$sessionStarted) {
            session_start();
            self::$sessionStarted = true;
        }
    }

    public static function addRoute($path, $callback) {
        self::$routes[$path] = $callback;
    }

    public static function dispatch() {
        self::init();
        
        $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $request_uri = rtrim($request_uri, '/');

        foreach (self::$routes as $path => $callback) {
            if ($request_uri === $path) {
                call_user_func($callback);
                return;
            }
        }

        // 404 Not Found
        header("HTTP/1.0 404 Not Found");
        include '404.php';
        exit;
    }
}