<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Function to clean and validate input
    function cleanInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data === '' ? null : $data;
    }

    // Collect and clean all form data
    $patientData = [
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
        // Add all other fields similarly
        // For integer fields:
        'jobs_changed' => is_numeric($_POST['jobs_changed'] ?? null) ? (int)$_POST['jobs_changed'] : null,
        'schools_changed' => is_numeric($_POST['schools_changed'] ?? null) ? (int)$_POST['schools_changed'] : null
    ];

    try {
        include_once 'db-conn.php'; // Include your database connection file
        $db = getDBConnection();

        // Build the SQL dynamically based on provided values
        $columns = [];
        $values = [];
        $placeholders = [];
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

        foreach ($patientData as $key => $value) {
            if ($value !== null) {
                $columns[] = $key;
                $placeholders[] = ":$key";
                $values[":$key"] = $value;
            }
        }

        // Add timestamp fields if not already present
        if (!in_array('created_at', $columns)) {
            $columns[] = 'created_at';
            $placeholders[] = 'CURRENT_TIMESTAMP';
        }
        if (!in_array('updated_at', $columns)) {
            $columns[] = 'updated_at';
            $placeholders[] = 'CURRENT_TIMESTAMP';
        }

        $sql = "INSERT INTO patient_case_histories (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $db->prepare($sql);
        $stmt->execute($values);

        $_SESSION['success'] = "Patient record saved successfully!";
        header('Location: /show'); // Redirect after successful submission
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        // Log the error: error_log($e->getMessage(), 3, "error.log");
    }
} else {
    $_SESSION['error'] = "Invalid Request!";
    header('Location: /create'); // Redirect if accessed directly
    exit;
}
