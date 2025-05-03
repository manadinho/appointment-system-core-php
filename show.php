<?php
$_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
include_once 'db-conn.php'; // Include your database connection file
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = getDBConnection();

// Fetch all patients
$stmt = $db->query("SELECT id, name, age, gender, father_name, marital_status, address, presenting_problems FROM patient_case_histories ORDER BY created_at DESC");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Patients</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="fonts/Linearicons/Font/demo-files/demo.css">
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/owl-carousel/assets/owl.carousel.css">
    <link rel="stylesheet" href="plugins/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="plugins/apexcharts-bundle/dist/apexcharts.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/datatables.css">
    <link rel="stylesheet" href="/css/notify.min.css">
    <link rel="stylesheet" href="/css/amsify.suggestags.css">
    <style>
        .action-btns {
            white-space: nowrap;
        }
        .dataTables_wrapper {
            padding: 20px;
        }

        .new-patient-btn {
            float: right;
            margin-bottom: 10px;    
        }

        .logout-btn {
            float: right;
            position: relative;
            bottom: 35px;
            left: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ps-section__header">
            <h2> All Patients</h2>
        </div>
        <!-- Add this to your dashboard where you want the logout button to appear -->
        <a href="logout.php" class="btn btn-danger logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
        <div class="ps-section__actions new-patient-btn"><a class="ps-btn success" href="create.php"><i class="icon icon-plus mr-2"></i>New Patient</a></div>
    </div>
<div class="table-responsive">
    <table class="table ps-table" id="myDataTable">
        <thead>
            <tr class="smaller-fonts">
                <!-- Personal Information -->
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Marital Status</th>
                <th>Address</th>
                <th>Father's Name</th>
                <!-- Presenting Problems -->
                <th>Problems</th>
                <!-- Created By -->
                <th>Created By</th>
                <!-- Actions -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <!-- Personal Information -->
                        <td><?php echo htmlspecialchars($patient['name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($patient['age'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($patient['gender'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($patient['marital_status'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($patient['address'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($patient['father_name'] ?? ''); ?></td>
                        
                        <!-- Presenting Problems -->
                        <td><?php echo htmlspecialchars($patient['presenting_problems'] ?? ''); ?></td>
                        <!-- Created By -->
                        <td><?php echo htmlspecialchars($patient['created_by_name'] ?? ''); ?></td>

                        <!-- Actions -->
                        <td>
                        <div class="dropdown">
                            <button class="btn btn-link p-0" type="button" id="patientActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i> <!-- Bootstrap Icons ellipsis -->
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="patientActionsDropdown">
                                <li><a class="dropdown-item" href="/edit?id=<?= htmlspecialchars($patient['id']) ?>"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                                <!-- <li><a class="dropdown-item" href="#" onclick="showPatientDetail('<?= htmlspecialchars($patient['id']) ?>')"><i class="bi bi-eye me-2"></i> View Details</a></li> -->
                                <li>
                                    <form action="/delete" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($patient['id']) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this patient?')">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>                            
                            </ul>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No patients found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this patient record? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Toastr.js CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Action functions
function showPatientDetail(patientId) {
    // Example: window.location.href = 'details.php?id=' + encodeURIComponent(patientId);
    console.log("Viewing patient:", patientId);
}

function deletePatient(patientId) {
    if (confirm('Are you sure you want to delete this patient record?')) {
        // Example: window.location.href = 'delete.php?id=' + encodeURIComponent(patientId);
        console.log("Deleting patient:", patientId);
    }
}
</script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#patientsTable').DataTable({
                responsive: true,
                dom: '<"top"lf>rt<"bottom"ip>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search patients...",
                }
            });
            
            // Delete button click handler
            let patientIdToDelete;
            $('.delete-btn').click(function() {
                patientIdToDelete = $(this).data('id');
                $('#deleteModal').modal('show');
            });
            console.log('ID:' . patientIdToDelete);
            // Confirm delete handler
            $('#confirmDelete').click(function() {
                $.ajax({
                    url: 'delete.php',
                    type: 'POST',
                    data: { id: patientIdToDelete },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error deleting patient: ' + error);
                    }
                });
            });
        });

        <?php if (isset($_SESSION['success'])): ?>
            toastr.success("<?php echo $_SESSION['success']; ?>");
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            toastr.error("<?php echo $_SESSION['error']; ?>");
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
</script>

</body>
</html>