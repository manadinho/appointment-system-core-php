<?php
function getDBConnection() {
    static $db = null;
    
    if ($db === null) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=appointment', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $db;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function login($email, $password) {
    // Validate input first
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email and password are required";
        return false;
    }

    try {
        $db = getDBConnection();
        
        // Use FETCH_NUM to reduce memory usage
        $stmt = $db->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        
        // Fetch just one row (LIMIT 1) and only the columns we need
        $user = $stmt->fetch(PDO::FETCH_NUM);
        
        if ($user && password_verify($password, $user[1])) { // [0] is id, [1] is password
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Store minimal session data
            $_SESSION = [
                'user_id' => $user[0],
                'user_email' => $email,
                'logged_in' => true
            ];
            
            // Free memory
            unset($user);
            $stmt->closeCursor();
            
            return true;
        }
        
        // Free memory
        $stmt->closeCursor();
        
        $_SESSION['login_error'] = "Invalid credentials";
        return false;
        
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = "System error. Please try again.";
        return false;
    }
}

function registerUser($email, $password) {
    $db = getDBConnection();
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return false; // Email already exists
    }
    
    // Insert new user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    
    return $stmt->execute([ $email, $hashed_password]);
}

function logout() {
    session_destroy();
    redirect('login.php');
}