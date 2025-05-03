<?php
include_once 'db-conn.php'; // Include your database connection file

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token (important for security)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $db = getDBConnection();

    // Function to clean and validate input
    function cleanInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data === '' ? null : $data;
    }

    // Get patient ID
    $patientId = $_POST['patient_id'] ?? null;
    if (!$patientId || !is_numeric($patientId)) {
        die("Invalid patient ID");
    }

    // Collect and clean all form data
    $patientData = [
        // Personal Information
        'name' => cleanInput($_POST['name'] ?? null),
        'age' => is_numeric($_POST['age'] ?? null) ? (int)$_POST['age'] : null,
        'gender' => cleanInput($_POST['gender'] ?? null),
        'marital_status' => cleanInput($_POST['marital_status'] ?? null),
        'address' => cleanInput($_POST['address'] ?? null),
        'father_name' => cleanInput($_POST['father_name'] ?? null),
        'spouse_name' => cleanInput($_POST['spouse_name'] ?? null),
        'siblings' => cleanInput($_POST['siblings'] ?? null),
        'children' => cleanInput($_POST['children'] ?? null),
        'family_structure' => cleanInput($_POST['family_structure'] ?? null),
        'head_of_family' => cleanInput($_POST['head_of_family'] ?? null),
        'relationship' => cleanInput($_POST['relationship'] ?? null),
        'informant' => cleanInput($_POST['informant'] ?? null),
        
        // Case History Sheet
        'presenting_problems' => cleanInput($_POST['problems'] ?? null),
        'history_of_problems' => cleanInput($_POST['history_of_problems'] ?? null),
        'complaints' => cleanInput($_POST['complaints'] ?? null),
        'frequency' => cleanInput($_POST['frequency'] ?? null),
        'intensity' => cleanInput($_POST['intensity'] ?? null),
        'duration' => cleanInput($_POST['duration'] ?? null),
        'prior_treatments' => cleanInput($_POST['prior_treatments'] ?? null),
        'medical_history' => cleanInput($_POST['medical_history'] ?? null),
        
        // Family Background
        'family_history' => cleanInput($_POST['family_history'] ?? null),
        'family_psychiatry_history' => cleanInput($_POST['family_psychiatry_history'] ?? null),
        'family_medical_history' => cleanInput($_POST['family_medical_history'] ?? null),
        'family_relationship' => cleanInput($_POST['family_relationship'] ?? null),
        'client_family_relationship' => cleanInput($_POST['client_family'] ?? null),
        'family_client_relationship' => cleanInput($_POST['family_client'] ?? null),
        
        // Marital History
        'marital_details' => cleanInput($_POST['marital_details'] ?? null),
        'spouse_children' => cleanInput($_POST['spouse_children'] ?? null),
        'client_spouse_relationship' => cleanInput($_POST['client_spouse'] ?? null),
        'spouse_client_relationship' => cleanInput($_POST['spouse_client'] ?? null),
        
        // Work History
        'client_colleagues_relationship' => cleanInput($_POST['client_colleagues'] ?? null),
        'colleagues_client_relationship' => cleanInput($_POST['colleagues_client'] ?? null),
        'jobs_changed' => is_numeric($_POST['jobs_changed'] ?? null) ? (int)$_POST['jobs_changed'] : null,
        'job_change_reasons' => cleanInput($_POST['job_reasons'] ?? null),
        
        // Education History
        'schools_changed' => is_numeric($_POST['schools_changed'] ?? null) ? (int)$_POST['schools_changed'] : null,
        'school_change_reasons' => cleanInput($_POST['school_reasons'] ?? null),
        'school_name' => cleanInput($_POST['school_name'] ?? null),
        'client_peers_relationship' => cleanInput($_POST['client_peers'] ?? null),
        'client_teachers_relationship' => cleanInput($_POST['client_teachers'] ?? null),
        'social_history' => cleanInput($_POST['social_history'] ?? null),
        
        // Observations
        'orientation' => cleanInput($_POST['orientation'] ?? null),
        'sleep_issues' => cleanInput($_POST['sleep'] ?? null),
        'thoughts' => cleanInput($_POST['thoughts'] ?? null),
        'behavior' => cleanInput($_POST['behavior'] ?? null),
        
        // Diagnosis & Recommendations
        'tentative_diagnosis' => cleanInput($_POST['diagnosis'] ?? null),
        'management_plan' => cleanInput($_POST['recommendations'] ?? null),
        
        // Timestamps will be handled by database defaults or separately
    ];
    
    try {
        // Build the SQL dynamically based on provided values
        $setClauses = [];
        $values = [];

        foreach ($patientData as $key => $value) {
            if ($key !== 'id' && $value !== null) {
                $setClauses[] = "$key = :$key";
                $values[":$key"] = $value;
            }
        }

        // Add updated_at timestamp
        $setClauses[] = "updated_at = CURRENT_TIMESTAMP";

        $sql = "UPDATE patient_case_histories SET " . implode(', ', $setClauses) . " WHERE id = :id";
        $values[':id'] = $patientId;

        $stmt = $db->prepare($sql);
        $stmt->execute($values);

        // Set success message and redirect
        $_SESSION['success'] = "Patient record updated successfully!";
        header("Location: /show");
        exit;

    } catch (PDOException $e) {
        // Set error message and redirect back
        $_SESSION['error'] = "Error updating record: " . $e->getMessage();
        header("Location: /edit");
        exit;
    }
} else {
    // Not a POST request, redirect to homepage
    header("Location: /edit");
    exit;
}
