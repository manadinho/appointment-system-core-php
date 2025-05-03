<?php
include_once 'db-conn.php'; // Include your database connection file
$db = getDBConnection();


// Get patient ID from URL and validate
$patientId = $_GET['id'] ?? null;
if (!$patientId || !is_numeric($patientId)) {
    $_SESSION['error'] = "Invalid patient ID";
    header("Location: /show");
    exit;
}

// Fetch patient data
$stmt = $db->prepare("SELECT * FROM patient_case_histories WHERE id = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    $_SESSION['error'] = "Patient not found";
    header("Location: /show");
    exit;
}

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper function to safely output values
function safeOutput($value)
{
    return $value !== null ? htmlspecialchars($value) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="author" content="">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <link href="apple-touch-icon.png" rel="apple-touch-icon">
  <link href="favicon.png" rel="icon">
  <title>Edit</title>
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- <link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css"> -->
  <!-- <link rel="stylesheet" href="fonts/Linearicons/Font/demo-files/demo.css"> -->
  <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="plugins/owl-carousel/assets/owl.carousel.css"> -->
  <!-- <link rel="stylesheet" href="plugins/select2/dist/css/select2.min.css"> -->
  <!-- <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css"> -->
  <!-- <link rel="stylesheet" href="plugins/apexcharts-bundle/dist/apexcharts.css"> -->
  <link rel="stylesheet" href="/css/style.css">
  <!-- <link rel="stylesheet" href="/css/datatables.css"> -->
  <!-- <link rel="stylesheet" href="/css/notify.min.css"> -->
  <link rel="stylesheet" href="/css/amsify.suggestags.css">
    <style>
        /* Hide all steps by default */
        .tab {
            display: none;
        }

        /* Hide all steps by default */
        .tab {
            display: none;
        }

        /* Step indicators */
        .step {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbbbbb;
            border: none;
            border-radius: 50%;
            display: inline-block;
            opacity: 0.5;
        }

        .step.active {
            opacity: 1;
        }

        .step.finish {
            background-color: #04AA6D;
        }

        /* Section Navigation Bar */
        .section-navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .section-link {
            cursor: pointer;
            padding: 10px;
            color: #666;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .section-link:hover {
            color: #04AA6D;
        }

        .section-link.active {
            color: #04AA6D;
            border-bottom: 2px solid #04AA6D;
        }

        /* Hide all tabs by default */
        .tab {
            display: none;
        }

        /* Show the active tab */
        .tab.active {
            display: block;
        }

        .smaller-fonts {
            font-size: 12px;
        }

        th, tr {
            white-space: nowrap; /* Prevents text from wrapping to the next line */
            text-align: left; 
            padding: 8px; 
        }

        .detail-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            color: #26901b;
            border-bottom: 2px solid #26901b;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 24px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-item label {
            font-weight: bold;
            color: #555;
            flex: 1;
        }
        .detail-item p {
            margin: 0;
            color: #333;
            flex: 2;
        }
        .back-button {
            display: block;
            width: 100px;
            margin: 20px auto 0;
            padding: 10px;
            text-align: center;
            background: #26901b;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .back-button:hover {
            background:rgb(36, 196, 19);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: #f5f5f5;
            padding: 20px;
        }

        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }


        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }

        .form-group {
            padding: 10px;
        }

        /* Three columns for short fields */
        .form-group.short {
            width: 33.333%;
        }

        /* Full width for textareas and longer fields */
        .form-group.full {
            width: 100%;
        }

        /* Two columns for medium fields */
        .form-group.medium {
            width: 50%;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .ps-btn {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .form-group.short {
                width: 100%;
            }
            
            .form-group.medium {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="ps-site-overlay"></div>
  <main class="ps-main">
    <div class="ps-main__wrapper">
    <section class="ps-card">
        <div class="ps-card__header">
            <h3>Edit Form</h3>
        </div>
        <div class="ps-card__content section">
        <form class="ps-form--account-settings" id="case-history-form" action="/update" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient['id']) ?>">   
            
            <!-- Section Navigation Bar -->
            <div class="section-navigation smaller-fonts">
                <span class="section-link active" onclick="showSection(0)">Personal Information</span>
                <span class="section-link" onclick="showSection(1)">Case History Sheet</span>
                <span class="section-link" onclick="showSection(2)">Birth and Early Childhood</span>
                <span class="section-link" onclick="showSection(3)">Marital History</span>
                <span class="section-link" onclick="showSection(4)">Work History</span>
                <span class="section-link" onclick="showSection(5)">Education History</span>
                <span class="section-link" onclick="showSection(6)">Observations</span>
            </div>

            <!-- Personal Information -->
            <div class="tab active">
                <h4>Personal Information</h4>
                <div class="row">
                    <div class="form-group short">
                        <label>Name</label>
                        <input class="form-control" name="name" type="text" value="<?= htmlspecialchars($patient['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group short">
                        <label>Age</label>
                        <input class="form-control" name="age" type="number" value="<?= htmlspecialchars($patient['age'] ?? '') ?>" required>
                    </div>
                    <div class="form-group short">
                        <label>Gender</label>
                        <select class="form-control" name="gender">
                            <option value="Male" <?= ($patient['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($patient['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= ($patient['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group short">
                        <label>Marital Status</label>
                        <select class="form-control" name="marital_status">
                            <option value="Single" <?= ($patient['marital_status'] ?? '') == 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= ($patient['marital_status'] ?? '') == 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Divorced" <?= ($patient['marital_status'] ?? '') == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                            <option value="Widowed" <?= ($patient['marital_status'] ?? '') == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="Separated" <?= ($patient['marital_status'] ?? '') == 'Separated' ? 'selected' : '' ?>>Separated</option>
                        </select>
                    </div>
                    <div class="form-group short">
                        <label>Father's Name</label>
                        <input class="form-control" name="father_name" type="text" value="<?= htmlspecialchars($patient['father_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group short">
                        <label>Spouse's Name</label>
                        <input class="form-control" name="spouse_name" type="text" value="<?= htmlspecialchars($patient['spouse_name'] ?? '') ?>">
                    </div>
                    <div class="form-group full">
                        <label>Present Address</label>
                        <textarea class="form-control" name="address" required><?= htmlspecialchars($patient['address'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group short">
                        <label>Siblings (M/F)</label>
                        <input class="form-control" name="siblings" type="text" value="<?= htmlspecialchars($patient['siblings'] ?? '') ?>">
                    </div>
                    <div class="form-group short">
                        <label>Children (Sex/Age)</label>
                        <input class="form-control" name="children" type="text" value="<?= htmlspecialchars($patient['children'] ?? '') ?>">
                    </div>
                    <div class="form-group short">
                        <label>Family Structure</label>
                        <input class="form-control" name="family_structure" type="text" value="<?= htmlspecialchars($patient['family_structure'] ?? '') ?>">
                    </div>
                    <div class="form-group short">
                        <label>Head Of Family</label>
                        <input class="form-control" name="head_of_family" type="text" value="<?= htmlspecialchars($patient['head_of_family'] ?? '') ?>">
                    </div>
                    <div class="form-group short">
                        <label>Relationship</label>
                        <input class="form-control" name="relationship" type="text" value="<?= htmlspecialchars($patient['relationship'] ?? '') ?>">
                    </div>
                    <div class="form-group short">
                        <label>Informant</label>
                        <input class="form-control" name="informant" type="text" value="<?= htmlspecialchars($patient['informant'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Step 2: Family Information -->
            <div class="tab">
                <h4>Case History Sheet</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Presenting Problems</label>
                            <textarea class="form-control" name="problems"><?= htmlspecialchars($patient['presenting_problems'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>History Of Problems</label>
                            <textarea class="form-control" name="history_of_problems"><?= htmlspecialchars($patient['history_of_problems'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Complaints</label>
                            <input class="form-control" name="complaints" type="text" value="<?= htmlspecialchars($patient['complaints'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Frequency</label>
                            <input class="form-control" name="frequency" type="text" value="<?= htmlspecialchars($patient['frequency'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Intensity</label>
                            <input class="form-control" name="intensity" type="text" value="<?= htmlspecialchars($patient['intensity'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Duration</label>
                            <input class="form-control" name="duration" type="text" value="<?= htmlspecialchars($patient['duration'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Prior Treatments</label>
                            <textarea class="form-control" name="prior_treatments"><?= htmlspecialchars($patient['prior_treatments'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Medical History</label>
                            <textarea class="form-control" name="medical_history"><?= htmlspecialchars($patient['medical_history'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Medical History -->
            <div class="tab">
                <h4>Birth and Early Childhood</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Family History</label>
                            <textarea class="form-control" name="family_history"><?= htmlspecialchars($patient['family_history'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Family Psychiatry History</label>
                            <textarea class="form-control" name="family_psychiatry_history"><?= htmlspecialchars($patient['family_psychiatry_history'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Family Medical History</label>
                            <textarea class="form-control" name="family_medical_history"><?= htmlspecialchars($patient['family_medical_history'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Family Relationship</label>
                            <textarea class="form-control" name="family_relationship"><?= htmlspecialchars($patient['family_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Client Relationship with Family</label>
                            <textarea class="form-control" name="client_family"><?= htmlspecialchars($patient['client_family_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Family Relationship with Client</label>
                            <textarea class="form-control" name="family_client"><?= htmlspecialchars($patient['family_client_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Marital History -->
            <div class="tab">
                <h4>Marital History</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Marital Details (occupation of spouse, conflicts, etc.)</label>
                            <textarea class="form-control" name="marital_details"><?= htmlspecialchars($patient['marital_details'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Spouse & No. of Children</label>
                            <input class="form-control" name="spouse_children" type="text" value="<?= htmlspecialchars($patient['spouse_children'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Client Relationship with Spouse</label>
                            <textarea class="form-control" name="client_spouse"><?= htmlspecialchars($patient['client_spouse_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Spouse Relationship with Client</label>
                            <textarea class="form-control" name="spouse_client"><?= htmlspecialchars($patient['spouse_client_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Work History -->
            <div class="tab">
                <h4>Work History</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Client Relationship with Colleagues</label>
                            <textarea class="form-control" name="client_colleagues"><?= htmlspecialchars($patient['client_colleagues_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Colleagues Relationship with Client</label>
                            <textarea class="form-control" name="colleagues_client"><?= htmlspecialchars($patient['colleagues_client_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Number of Jobs Changed</label>
                            <input class="form-control" name="jobs_changed" type="number" value="<?= htmlspecialchars($patient['jobs_changed'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Reasons for Changing Jobs</label>
                            <textarea class="form-control" name="job_reasons"><?= htmlspecialchars($patient['job_change_reasons'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 6: Education History -->
            <div class="tab">
                <h4>Education History</h4>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Number of Schools Changed</label>
                            <input class="form-control" name="schools_changed" type="number" value="<?= htmlspecialchars($patient['schools_changed'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>School Name</label>
                            <input class="form-control" name="school_name" type="text" value="<?= htmlspecialchars($patient['school_name'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Reasons for Changing Schools</label>
                            <textarea class="form-control" name="school_reasons"><?= htmlspecialchars($patient['school_change_reasons'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Client Relationship with Peers</label>
                            <textarea class="form-control" name="client_peers"><?= htmlspecialchars($patient['client_peers_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Client Relationship with Teachers</label>
                            <textarea class="form-control" name="client_teachers"><?= htmlspecialchars($patient['client_teachers_relationship'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <h4>Social History</h4>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Social History (Neighbors, Friends, Relatives)</label>
                            <textarea class="form-control" name="social_history"><?= htmlspecialchars($patient['social_history'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 7: Observations -->
            <div class="tab">
                <h4>Observations</h4>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Orientation</label>
                            <input class="form-control" name="orientation" type="text" value="<?= htmlspecialchars($patient['orientation'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Sleep Issues</label>
                            <input class="form-control" name="sleep" type="text" value="<?= htmlspecialchars($patient['sleep_issues'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Thoughts</label>
                            <textarea class="form-control" name="thoughts"><?= htmlspecialchars($patient['thoughts'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Behavior</label>
                            <textarea class="form-control" name="behavior"><?= htmlspecialchars($patient['behavior'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <h4>Diagnosis & Recommendations</h4>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Tentative Diagnosis</label>
                            <input class="form-control" name="diagnosis" type="text" value="<?= htmlspecialchars($patient['tentative_diagnosis'] ?? '') ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Management Plan</label>
                            <textarea class="form-control" name="recommendations"><?= htmlspecialchars($patient['management_plan'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="ps-form__submit" style="display: flex;justify-content: space-between;float: inline-end;gap: 10px;">
                <div>
                    <button type="button" class="ps-btn" id="prevBtn" onclick="prevSection()" style="display: none;">Previous</button>
                </div>
                <div>
                    <button type="submit" class="ps-btn success">Update Patient</button>
                </div>
                <div>
                    <button type="button" class="ps-btn" id="nextBtn" onclick="nextSection()" style="display: block;float: right;">Next</button>
                </div>
            </div>
        </form>
</section>
    </div>
</main>
<script src="plugins/jquery.min.js"></script>
<script src="plugins/jquery-ui.min.js"></script>
<script src="plugins/touch.js"></script>
<script src="plugins/popper.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="plugins/jquery.matchHeight-min.js"></script>
<script src="plugins/select2/dist/js/select2.full.min.js"></script>
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<script src="plugins/apexcharts-bundle/dist/apexcharts.min.js"></script>
<script src="js/chart.js"></script>
<script src="js/datatables.js"></script>
<script src="js/pusher.js"></script>
<script src="js/notify.js"></script>
<!-- custom code -->
<script src="js/main.js"></script>
<script src="js/jquery.validate.js"></script>
<script src="js/jquery.amsify.suggestags.js"></script>
  <script>
    $(document).ready(function() {
      $('#full-view-checkbox').click(function() {
        if ($(this).prop('checked') == true) {
          $('.ps-main__sidebar').fadeOut("slow");
        } else {
          $('.ps-main__sidebar').fadeIn("slow");
        }

      })
    })

    // TOOL TIP ACTIVATION CODE
    $(function() {
      $('[data-toggle="tooltip"]').tooltip()
    })

    // ADD NEW PATIENT
    // function addPatient(id) {
    //   spinnerStart()
    //   const route = "{{ route('patients.edit', ['id' => ':id']) }}".replace(':id', id);
    //   $.ajax({
    //     url: route,
    //     method: "GET",
    //     data: {
    //       id
    //     },
    //     success: response => {
    //       spinnerEnd();
    //       if (response.success) {
    //         toastMessage('Success', 'Patient added successfully.', 'success');
    //       }
    //       else {
    //         toastMessage('Error', 'Something went wrong!', 'warning');
    //       }
    //     }
    //   })
    // }

    // GET ALL APPOINTMENTS
    function getAllAppointments(type = 'all', spinner = null) {
      if (spinner != null) {
        spinnerStart();
      }
      $.ajax({
        url: "{{ route('appointments.all-appointments') }}",
        method: "GET",
        success: response => {
          spinnerEnd();
          if (response.success) {
            $('#new-appointments').html('');
            $('#new-appointments').html(response.newAppointments);
            if (type == 'all') {
              $('#cancelled-appointments').html('');
              $('#done-appointments').html('');
              $('#done-appointments').html(response.doneAppointmentsHtml);
              $('#cancelled-appointments').html(response.cancelledAppointments);
            }
          }
        }
      })
    }

    // OPEN APPOINTMENT DETAIL MODEL
    function appointmentDetail(element) {
      spinnerStart()
      appointmentID = $(element).data('id');
      $.ajax({
        url: "{{ route('appointments.appointment-detail') }}",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        method: 'POST',
        data: {
          appointmentID
        },
        success: response => {
          spinnerEnd()
          if (response.success) {
            $('#appointmentSystemModalBody').html(response.view);
            $('#appointmentSystemModal').modal('toggle');
          } else {
            toastMessage('error', 'Something went wrong!', 'warning');
          }
        }
      });
    }

    //Spinner Start
    function spinnerStart() {
      $("#overlay").fadeIn(300);
    }

    function spinnerEnd() {
      $("#overlay").fadeOut(300);
    }

  function nextPrev(n) {
      var x = document.getElementsByClassName("tab");

      // Exit the function if any field in the current tab is invalid:
      if (n == 1 && !validateForm()) return false;

      // Hide the current tab:
      x[currentTab].style.display = "none";

      // Increase or decrease the current tab by 1:
      currentTab = currentTab + n;

      // If you have reached the end of the form, submit it:
      if (currentTab >= x.length) {
          document.getElementById("case-history-form").submit();
          return false;
      }

      // Otherwise, display the correct tab:
      showTab(currentTab);
  }

  function validateForm() {
      var x, y, i, valid = true;
      x = document.getElementsByClassName("tab");
      y = x[currentTab].getElementsByTagName("input");

      // Loop through all input fields in the current tab:
      for (i = 0; i < y.length; i++) {
          if (y[i].value == "" && y[i].hasAttribute("required")) {
              y[i].className += " invalid"; // Add an "invalid" class
              valid = false;
          }
      }

      // If the valid status is true, mark the step as finished and valid:
      if (valid) {
          document.getElementsByClassName("step")[currentTab].className += " finish";
      }
      return valid;
  }

  function fixStepIndicator(n) {
      var i, x = document.getElementsByClassName("step");
      for (i = 0; i < x.length; i++) {
          x[i].className = x[i].className.replace(" active", "");
      }
      x[n].className += " active";
  }

    // Function to show a specific section
    function showSection(n) {
        // Hide all tabs
        var tabs = document.getElementsByClassName("tab");
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].style.display = "none";
        }

        // Show the selected tab
        tabs[n].style.display = "block";

        // Update the active section link
        var sectionLinks = document.getElementsByClassName("section-link");
        for (var j = 0; j < sectionLinks.length; j++) {
            sectionLinks[j].classList.remove("active");
        }
        sectionLinks[n].classList.add("active");
    }

    // Initialize the form to show the first section
    showSection(0);

    document.addEventListener('keydown', function (event) {
        // Check if the pressed key is "Enter" (key code 13)
        if (event.keyCode === 13) {
            // Prevent the default form submission behavior
            event.preventDefault();

            // Get all focusable elements in the current tab
            const currentTab = document.querySelector('.tab[style*="display: block"]');
            const focusableElements = currentTab.querySelectorAll('input, textarea, select, button');

            // Find the currently focused element
            let focusedElement = document.activeElement;

            // Find the index of the currently focused element
            let focusedIndex = Array.from(focusableElements).indexOf(focusedElement);

            // Move focus to the next focusable element
            if (focusedIndex < focusableElements.length - 1) {
                focusableElements[focusedIndex + 1].focus();
            } else {
                // If it's the last field, move focus to the first field
                focusableElements[0].focus();
            }
        }
    });

    // Function to show the next section
function nextSection() {
    var currentTab = getCurrentTabIndex();
    var totalTabs = document.querySelectorAll('.tab').length;
    
    if (currentTab < totalTabs - 1) {
        showSection(currentTab + 1);
        updateButtonVisibility();
    }
}

// Function to show the previous section
function prevSection() {
    var currentTab = getCurrentTabIndex();
    
    if (currentTab > 0) {
        showSection(currentTab - 1);
        updateButtonVisibility();
    }
}

// Function to get the current tab index
function getCurrentTabIndex() {
    var tabs = document.getElementsByClassName("tab");
    for (var i = 0; i < tabs.length; i++) {
        if (tabs[i].style.display === "block") {
            return i;
        }
    }
    return 0;
}

// Function to update button visibility based on current tab
function updateButtonVisibility() {
    var currentTab = getCurrentTabIndex();
    var totalTabs = document.querySelectorAll('.tab').length;
    var prevBtn = document.getElementById("prevBtn");
    var nextBtn = document.getElementById("nextBtn");
    
    // Show/hide Previous button
    if (currentTab === 0) {
        prevBtn.style.display = "none";
    } else {
        prevBtn.style.display = "block";
    }
    
    // Show/hide Next button
    if (currentTab === totalTabs - 1) {
        nextBtn.style.display = "none";
    } else {
        nextBtn.style.display = "block";
    }
}

// Update button visibility when section changes
function showSection(n) {
    // Hide all tabs
    var tabs = document.getElementsByClassName("tab");
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].style.display = "none";
    }

    // Show the selected tab
    tabs[n].style.display = "block";

    // Update the active section link
    var sectionLinks = document.getElementsByClassName("section-link");
    for (var j = 0; j < sectionLinks.length; j++) {
        sectionLinks[j].classList.remove("active");
    }
    sectionLinks[n].classList.add("active");
    
    // Update button visibility
    updateButtonVisibility();
}
</script>
</body>
</html>