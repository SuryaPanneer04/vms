<?php
include("includes/config.php");
include("smtp.php");

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// AJAX Handler for Employee Pull
if (isset($_GET['ajax_pull_email'])) {
    ob_clean(); // Clear any previous output to ensure pure JSON
    $email = trim($_GET['ajax_pull_email']);
    $stmt = $con->prepare("SELECT * FROM employee_master WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    if ($employee) {
        echo json_encode(['status' => 'success', 'data' => $employee]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Employee not found in master records']);
    }
    exit();
}

$message = "";

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $plain_password = $_POST['password']; 
    $password = password_hash($plain_password, PASSWORD_DEFAULT);
    $dept = $_POST['department'];
    $designation = $_POST['designation'];
    $location = $_POST['location'];
    $role = $_POST['role'];
    $contact = $_POST['contact_no'];
    $status = $_POST['status'];
    $reporting_manager = !empty($_POST['reporting_manager']) ? $_POST['reporting_manager'] : 0;
    
    if (!empty($name) && !empty($email)) {
        try {
            $check_stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->execute([$email]);
            if ($check_stmt->fetch()) {
                $message = "<div class='alert alert-warning fw-bold'><i class='fas fa-exclamation-circle me-2'></i> Email already exists.</div>";
            } else {
                $stmt = $con->prepare("INSERT INTO users (full_name, email, password, department, designation, location, reporting_manager, role, contact_no, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $password, $dept, $designation, $location, $reporting_manager, $role, $contact, $status]);
                
                $placeholders = [
                    'full_name' => $name,
                    'email' => $email,
                    'password' => $plain_password
                ];
                sendTemplateMail($email, 'user_credentials', $placeholders);
                $message = "<div class='alert alert-success'>User added successfully!</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $dept = $_POST['department'];
    $designation = $_POST['designation'];
    $location = $_POST['location'];
    $role = $_POST['role'];
    $contact = $_POST['contact_no'];
    $status = $_POST['status'];
    $reporting_manager = !empty($_POST['reporting_manager']) ? $_POST['reporting_manager'] : 0;

    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $con->prepare("UPDATE users SET full_name=?, email=?, password=?, department=?, designation=?, location=?, reporting_manager=?, role=?, contact_no=?, status=? WHERE id=?");
            $stmt->execute([$name, $email, $password, $dept, $designation, $location, $reporting_manager, $role, $contact, $status, $id]);
        } else {
            $stmt = $con->prepare("UPDATE users SET full_name=?, email=?, department=?, designation=?, location=?, reporting_manager=?, role=?, contact_no=?, status=? WHERE id=?");
            $stmt->execute([$name, $email, $dept, $designation, $location, $reporting_manager, $role, $contact, $status, $id]);
        }
        $message = "<div class='alert alert-success'>User updated successfully!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

$departments = $con->query("SELECT dept_name, dept_color FROM departments WHERE status = 1 ORDER BY dept_name ASC")->fetchAll();
$plants = $con->query("SELECT plant_location FROM plants ORDER BY plant_location ASC")->fetchAll();
$designation_list = $con->query("SELECT designation_name FROM designation_master WHERE status = 1 ORDER BY designation_name ASC")->fetchAll();
$all_users = $con->query("SELECT id, full_name, department FROM users WHERE designation = 'Manager' ORDER BY full_name ASC")->fetchAll();

// Fetch Users for List
$users = $con->query("SELECT u.*, m.full_name as manager_name, d.dept_color FROM users u LEFT JOIN users m ON u.reporting_manager = m.id LEFT JOIN departments d ON u.department = d.dept_name ORDER BY u.id DESC")->fetchAll();

include("includes/header.php");
include("includes/sidebar.php");
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">

<style>
    .filter-card {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 25px;
        border: 1px solid #f1f3f5;
        box-shadow: 0 2px 15px rgba(0,0,0,0.03);
    }
    .filter-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 800;
        color: #adb5bd;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-label i { font-size: 0.9rem; color: #4361ee; }
    .desig-tabs { display: flex; flex-wrap: wrap; gap: 8px; }
    .desig-tab { 
        padding: 7px 16px; 
        background: #f8f9fa; 
        border: 1px solid #e9ecef; 
        border-radius: 8px; 
        font-size: 0.82rem; 
        font-weight: 600; 
        cursor: pointer; 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #495057;
    }
    .desig-tab:hover { 
        background: #fff; 
        border-color: #4361ee; 
        color: #4361ee; 
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
    }
    .desig-tab.active { 
        background: #4361ee; 
        border-color: #4361ee; 
        color: #fff; 
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }
    .dt-buttons { margin-bottom: 15px; }
</style>

<div class="content">
    <div class="container-fluid content-animate">
        <h2 class="fw-bold text-dark mb-4">Staff & User Management</h2>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Add New User</h5>
                <form method="POST" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control bg-light border-0 py-2" placeholder="Pull from master..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="full_name" id="full_name" class="form-control bg-light border-0 py-2" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control bg-light border-0 py-2" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Contact No</label>
                        <input type="text" name="contact_no" id="contact_no" class="form-control bg-light border-0 py-2" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Designation</label>
                        <select name="designation" id="designation" class="form-select bg-light border-0 py-2" required>
                            <option value="">Select</option>
                            <?php foreach ($designation_list as $dl): ?>
                                <option value="<?= $dl['designation_name'] ?>"><?= $dl['designation_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Department</label>
                        <select name="department" id="department" class="form-select bg-light border-0 py-2" required>
                            <option value="">Select</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['dept_name'] ?>" style="background-color: <?= $d['dept_color'] ?>; color: #fff;">
                                    <?= $d['dept_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Role</label>
                        <select name="role" class="form-select bg-light border-0 py-2" required>
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                            <option value="timeoffice">Time Office</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Plant / Location</label>
                        <select name="location" class="form-select bg-light border-0 py-2" required>
                            <option value="">Select</option>
                            <?php foreach ($plants as $p): ?>
                                <option value="<?= $p['plant_location'] ?>"><?= $p['plant_location'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Reporting Manager</label>
                        <select name="reporting_manager" class="form-select bg-light border-0 py-2">
                            <option value="0">Select</option>
                            <?php foreach ($all_users as $au): ?>
                                <option value="<?= $au['id'] ?>"><?= $au['full_name'] ?> (<?= $au['department'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select bg-light border-0 py-2" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="add_user" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm">Add User</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($message) echo $message; ?>

        <!-- Designation Filters -->
        <div class="filter-card">
            <span class="filter-label"><i class="fas fa-filter"></i> Filter by Designation</span>
            <div class="desig-tabs">
                <div class="desig-tab active" data-filter="">All Staff</div>
                <?php foreach ($designation_list as $dl): ?>
                    <div class="desig-tab" data-filter="<?= htmlspecialchars($dl['designation_name']) ?>">
                        <?= htmlspecialchars($dl['designation_name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="userTable" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Plant</th>
                                <th>Manager</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><div class="fw-bold"><?= htmlspecialchars($u['full_name']) ?></div></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($u['designation']) ?></span></td>
                                <td>
                                    <span class="badge fw-normal" style="background: <?= !empty($u['dept_color']) ? $u['dept_color'] : '#f8f9fa' ?>; color: <?= !empty($u['dept_color']) ? '#fff' : '#212529' ?>;">
                                        <?= htmlspecialchars($u['department']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($u['location']) ?></td>
                                <td>
                                    <div class="small text-muted"><?= htmlspecialchars($u['manager_name'] ?: 'None') ?></div>
                                </td>
                                <td><span class="badge <?= $u['status'] == 'Active' ? 'bg-success' : 'bg-danger' ?>"><?= $u['status'] ?></span></td>
                                <td class="text-center">
                                    <button class="btn btn-dark btn-sm px-3 editUserBtn" data-id="<?= $u['id'] ?>" 
                                            data-name="<?= $u['full_name'] ?>" data-email="<?= $u['email'] ?>" 
                                            data-dept="<?= $u['department'] ?>" data-desig="<?= $u['designation'] ?>"
                                            data-loc="<?= $u['location'] ?>" data-role="<?= $u['role'] ?>"
                                            data-contact="<?= $u['contact_no'] ?>" data-status="<?= $u['status'] ?>"
                                            data-manager="<?= $u['reporting_manager'] ?>">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control bg-light border-0 py-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control bg-light border-0 py-2" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Designation</label>
                        <select name="designation" id="edit_designation" class="form-select bg-light border-0 py-2">
                            <?php foreach ($designation_list as $dl): ?>
                                <option value="<?= $dl['designation_name'] ?>"><?= $dl['designation_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Department</label>
                        <select name="department" id="edit_department" class="form-select bg-light border-0 py-2">
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d['dept_name'] ?>" style="background-color: <?= $d['dept_color'] ?>; color: #fff;">
                                    <?= $d['dept_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Plant / Location</label>
                        <select name="location" id="edit_location" class="form-select bg-light border-0 py-2">
                            <?php foreach ($plants as $p): ?>
                                <option value="<?= $p['plant_location'] ?>"><?= $p['plant_location'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Reporting Manager</label>
                        <select name="reporting_manager" id="edit_reporting_manager" class="form-select bg-light border-0 py-2">
                            <option value="0">None / Self</option>
                            <?php foreach ($all_users as $au): ?>
                                <option value="<?= $au['id'] ?>"><?= $au['full_name'] ?> (<?= $au['department'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Contact No</label>
                        <input type="text" name="contact_no" id="edit_contact_no" class="form-control bg-light border-0 py-2" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Role</label>
                        <select name="role" id="edit_role" class="form-select bg-light border-0 py-2" required>
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                            <option value="timeoffice">Time Office</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" id="edit_status" class="form-select bg-light border-0 py-2">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">New Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control bg-light border-0 py-2" placeholder="********">
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" name="update_user" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#userTable').DataTable({
        dom: "<'row'<'col-12'B>><'row mt-3'<'col-md-6'l><'col-md-6'f>>rt<'row'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm me-2', text: '<i class="fas fa-file-excel me-1"></i> Excel' },
            { extend: 'print', className: 'btn btn-dark btn-sm', text: '<i class="fas fa-print me-1"></i> Print' }
        ],
        pageLength: 10
    });

    $('.desig-tab').on('click', function() {
        const filterStr = $(this).data('filter');
        $('.desig-tab').removeClass('active');
        $(this).addClass('active');
        table.column(2).search(filterStr).draw();
    });

    $(document).on('click', '.editUserBtn', function() {
        $('#edit_user_id').val($(this).attr('data-id'));
        $('#edit_full_name').val($(this).attr('data-name'));
        $('#edit_email').val($(this).attr('data-email'));
        $('#edit_designation').val($(this).attr('data-desig'));
        $('#edit_department').val($(this).attr('data-dept'));
        $('#edit_location').val($(this).attr('data-loc'));
        $('#edit_reporting_manager').val($(this).attr('data-manager'));
        $('#edit_role').val($(this).attr('data-role'));
        $('#edit_contact_no').val($(this).attr('data-contact'));
        $('#edit_status').val($(this).attr('data-status'));
        new bootstrap.Modal($('#editUserModal')).show();
    });

    // Auto-pull from employee_master when email is entered
    $('#email').on('blur', function() {
        const email = $(this).val().trim();
        if (email === "") return;

        $.ajax({
            url: 'manage_users.php',
            type: 'GET',
            data: { ajax_pull_email: email },
            success: function(response) {
                if (response.status === 'success') {
                    const emp = response.data;
                    $('#full_name').val(emp.emp_name);
                    $('#contact_no').val(emp.contact_no);
                    $('#designation').val(emp.designation);
                    $('#department').val(emp.department);
                    
                    // Add a small success effect
                    $('#email').addClass('is-valid').removeClass('is-invalid');
                } else {
                    $('#email').addClass('is-invalid').removeClass('is-valid');
                }
            }
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>
