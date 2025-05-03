<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'router.php';

// Define routes
Router::addRoute('/', function() {
    include 'login.php';
});

Router::addRoute('/login', function() {
    include 'login.php';
});

Router::addRoute('/register', function() {
    include 'register.php';
});

Router::addRoute('/show', function() {
    include 'show.php';
});

Router::addRoute('/submit', function() {
    include 'submit.php';
});

Router::addRoute('/create', function() {
    include 'create.php';
});

Router::addRoute('/delete', function() {
    include 'delete.php';
});

Router::addRoute('/edit', function() {
    include 'edit.php';
});

Router::addRoute('/update', function() {
    include 'update.php';
});

Router::addRoute('/logout', function() {
    session_destroy();
    header("Location: /login");
    exit;
});

// Handle the request
Router::dispatch();