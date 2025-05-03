<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $db = new PDO('mysql:host=localhost;dbname=appointment', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare("DELETE FROM patient_case_histories WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $_SESSION['success'] = "Patient record deleted successfully!";
        header("Location: /show");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: /show");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: /show");
    exit;
}
