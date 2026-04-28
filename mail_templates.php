<?php
include("includes/config.php");

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Add Template
if (isset($_POST['add_template'])) {
    $name = $_POST['template_name'];
    $key = strtolower(str_replace(' ', '_', $name)) . '_' . rand(100, 999);
    $from = $_POST['from_email'];
    $cc = $_POST['cc_email'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $footer = $_POST['mail_footer'] ?? '';

    try {
        $stmt = $con->prepare("INSERT INTO mail_templates (template_key, template_name, from_email, cc_email, subject, body, mail_footer) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$key, $name, $from, $cc, $subject, $body, $footer]);
        $message = "<div class='alert alert-success'>New template added!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Handle Update Template
if (isset($_POST['update_template'])) {
    $id = $_POST['template_id'];
    $name = $_POST['template_name'];
    $from = $_POST['from_email'];
    $cc = $_POST['cc_email'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $footer = $_POST['mail_footer'] ?? '';

    try {
        $stmt = $con->prepare("UPDATE mail_templates SET template_name=?, from_email=?, cc_email=?, subject=?, body=?, mail_footer=? WHERE id=?");
        $stmt->execute([$name, $from, $cc, $subject, $body, $footer, $id]);
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <i class='fas fa-check-circle me-2'></i> Template updated successfully!
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Handle Delete Template
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $con->prepare("DELETE FROM mail_templates WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: mail_templates.php?msg=deleted");
    exit();
}
if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') $message = "<div class='alert alert-warning'>Template deleted!</div>";

// Fetch all templates
$templates = $con->query("SELECT * FROM mail_templates ORDER BY template_name ASC")->fetchAll(PDO::FETCH_ASSOC);

include("includes/header.php");
include("includes/sidebar.php");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Mail Templates</h2>
                <p class="text-muted small mb-0">Manage and customize your system's automated emails.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary rounded-3 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                    <i class="fas fa-plus me-2"></i> Add Template
                </button>
                <div class="bg-primary-subtle text-primary px-3 py-2 rounded-3 border border-primary-subtle shadow-sm">
                    <i class="fas fa-envelope-open-text me-2"></i>
                    <span class="fw-bold"><?= count($templates) ?> Active Templates</span>
                </div>
            </div>
        </div>

        <?php if ($message) echo $message; ?>

        <div class="row">
            <?php foreach ($templates as $t): ?>
                <div class="col-md-6 col-xl-4 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 template-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="template-icon me-3">
                                    <i class="fas fa-file-code"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($t['template_name']) ?></h5>
                            </div>
                            
                            <div class="mb-3">
                                <label class="small text-muted fw-bold mb-1 d-block">Subject Line</label>
                                <div class="bg-light p-2 rounded-3 border small text-dark text-truncate">
                                    <?= htmlspecialchars($t['subject']) ?>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <label class="small text-muted fw-bold mb-1 d-block">Placeholders</label>
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <?php 
                                        // Simple regex to find placeholders
                                        preg_match_all('/{{(.*?)}}/', $t['body'], $matches);
                                        $placeholders = array_unique($matches[1]);
                                        foreach ($placeholders as $p):
                                    ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            {{<?= htmlspecialchars($p) ?>}}
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <button class="btn btn-dark w-100 rounded-3 py-2 editTemplateBtn" 
                                        data-id="<?= $t['id'] ?>" 
                                        data-name="<?= htmlspecialchars($t['template_name']) ?>"
                                        data-from="<?= htmlspecialchars($t['from_email']) ?>"
                                        data-subject="<?= htmlspecialchars($t['subject']) ?>"
                                        data-cc="<?= htmlspecialchars($t['cc_email']) ?>"
                                        data-body="<?= htmlspecialchars($t['body']) ?>"
                                        data-footer="<?= htmlspecialchars($t['mail_footer']) ?>">
                                    <i class="fas fa-edit me-2"></i> Edit
                                </button>
                                <a href="?delete=<?= $t['id'] ?>" class="btn btn-outline-danger rounded-3" onclick="return confirm('Delete this template?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Edit Template Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Modify Mail Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="template_id" id="edit_template_id">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Template Display Name</label>
                        <input type="text" name="template_name" id="edit_template_name" class="form-control bg-light border-0 py-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">From Email (Override)</label>
                        <input type="email" name="from_email" id="edit_from_email" class="form-control bg-light border-0 py-2" placeholder="Default from settings">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">CC Email (Optional)</label>
                        <input type="text" name="cc_email" id="edit_cc_email" class="form-control bg-light border-0 py-2" placeholder="comma separated emails">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Email Subject</label>
                        <input type="text" name="subject" id="edit_subject" class="form-control bg-light border-0 py-2" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold d-flex justify-content-between">
                            Mail Body (HTML Supported)
                            <span class="text-primary fw-normal">Use {{placeholder_name}} for dynamic values</span>
                        </label>
                        <textarea name="body" id="edit_body" class="form-control bg-light border-0 py-2" rows="8" required></textarea>
                    </div>
                    <!-- Template specific footer removed from UI but kept in DB logic -->
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_template" class="btn btn-primary px-5 rounded-3 shadow">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Template Modal -->
<div class="modal fade" id="addTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Create New Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Template Display Name</label>
                        <input type="text" name="template_name" class="form-control bg-light border-0 py-2" placeholder="e.g. Visitor Check-out" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">From Email (Optional)</label>
                        <input type="email" name="from_email" class="form-control bg-light border-0 py-2" placeholder="Default from settings">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">CC Email (Optional)</label>
                        <input type="text" name="cc_email" class="form-control bg-light border-0 py-2" placeholder="comma separated emails">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Email Subject</label>
                        <input type="text" name="subject" class="form-control bg-light border-0 py-2" placeholder="Enter subject..." required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold d-flex justify-content-between">
                            Mail Body (HTML Supported)
                            <span class="text-primary fw-normal">Use {{placeholder_name}} for dynamic values</span>
                        </label>
                        <textarea name="body" class="form-control bg-light border-0 py-2" rows="8" placeholder="Type your email content here..." required></textarea>
                    </div>
                    <!-- Template specific footer removed from UI but kept in DB logic -->
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_template" class="btn btn-primary px-5 rounded-3 shadow">
                    <i class="fas fa-plus me-2"></i> Create Template
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .template-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .template-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; }
    .template-icon {
        width: 45px; height: 45px; background: #f8f9fa; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; color: #4361ee;
        font-size: 1.2rem; border: 1px solid #e9ecef;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.editTemplateBtn').on('click', function() {
        $('#edit_template_id').val($(this).data('id'));
        $('#edit_template_name').val($(this).data('name'));
        $('#edit_from_email').val($(this).data('from'));
        $('#edit_subject').val($(this).data('subject'));
        $('#edit_cc_email').val($(this).data('cc'));
        $('#edit_body').val($(this).data('body'));
        $('#edit_mail_footer').val($(this).data('footer'));
        new bootstrap.Modal($('#editTemplateModal')).show();
    });
});
</script>

<?php include("includes/footer.php"); ?>
