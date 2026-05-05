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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3">
                        <i class="fas fa-pen-nib fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Modify Mail Template</h5>
                        <p class="text-muted small mb-0">Customize how your automated emails look and feel.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="template_id" id="edit_template_id">
                <div class="row g-4">
                    <!-- Left: Form -->
                    <div class="col-xl-8">
                        <div class="card border-0 bg-light rounded-4 p-3 h-100">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Template Name</label>
                                    <input type="text" name="template_name" id="edit_template_name" class="form-control border-0 shadow-sm py-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">From Email (Override)</label>
                                    <input type="email" name="from_email" id="edit_from_email" class="form-control border-0 shadow-sm py-2" placeholder="Using default SMTP sender">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-dark">CC Recipients</label>
                                    <input type="text" name="cc_email" id="edit_cc_email" class="form-control border-0 shadow-sm py-2" placeholder="comma separated (e.g. boss@co.com, admin@co.com)">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-dark">Email Subject Line</label>
                                    <input type="text" name="subject" id="edit_subject" class="form-control border-0 shadow-sm py-2" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-dark">Email Message Body (HTML Allowed)</label>
                                    <textarea name="body" id="edit_body" class="form-control border-0 shadow-sm py-2 font-monospace" rows="10" style="font-size: 0.9rem;" required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-dark">Template Footer (Optional)</label>
                                    <textarea name="mail_footer" id="edit_mail_footer" class="form-control border-0 shadow-sm py-2" rows="2" placeholder="Specific footer for this template..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Guide -->
                    <div class="col-xl-4">
                        <div class="card border-0 bg-primary-subtle h-100 rounded-4">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Available Tags
                                </h6>
                                <p class="small text-muted mb-4">Paste these placeholders into your subject or body to inject dynamic data.</p>
                                
                                <div class="tag-group mb-4">
                                    <label class="small fw-bold text-primary opacity-75 d-block mb-2">VISITOR INFO</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <code class="tag-item">{{visitor_name}}</code>
                                        <code class="tag-item">{{company_name}}</code>
                                        <code class="tag-item">{{pass_no}}</code>
                                        <code class="tag-item">{{purpose}}</code>
                                        <code class="tag-item">{{in_time}}</code>
                                    </div>
                                </div>

                                <div class="tag-group mb-4">
                                    <label class="small fw-bold text-primary opacity-75 d-block mb-2">STAFF INFO</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <code class="tag-item">{{emp_name}}</code>
                                        <code class="tag-item">{{emp_dept}}</code>
                                        <code class="tag-item">{{emp_email}}</code>
                                    </div>
                                </div>

                                <div class="bg-white p-3 rounded-3 shadow-sm mt-auto">
                                    <h6 class="small fw-bold mb-2">Pro Tip:</h6>
                                    <p class="small text-muted mb-0">Use <code>&lt;b&gt;text&lt;/b&gt;</code> for bold and <code>&lt;br&gt;</code> for new lines to format your email.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success rounded-3 p-2 me-3">
                        <i class="fas fa-plus-circle fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Create New Template</h5>
                        <p class="text-muted small mb-0">Build a new automated email for your workflow.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="card border-0 bg-light rounded-4 p-3 h-100">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Template Name</label>
                                    <input type="text" name="template_name" class="form-control border-0 shadow-sm py-2" placeholder="e.g. Visitor Check-out" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">From Email (Optional)</label>
                                    <input type="email" name="from_email" class="form-control border-0 shadow-sm py-2" placeholder="Default sender">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">CC Email (Optional)</label>
                                    <input type="text" name="cc_email" class="form-control border-0 shadow-sm py-2" placeholder="comma separated emails">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Email Subject</label>
                                    <input type="text" name="subject" class="form-control border-0 shadow-sm py-2" placeholder="Enter subject..." required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Mail Body</label>
                                    <textarea name="body" class="form-control border-0 shadow-sm py-2 font-monospace" rows="10" placeholder="Type your email content here..." required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Mail Footer</label>
                                    <textarea name="mail_footer" class="form-control border-0 shadow-sm py-2" rows="2" placeholder="Footer text..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                         <div class="card border-0 bg-success-subtle h-100 rounded-4">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="fas fa-magic me-2"></i>Magic Tags
                                </h6>
                                <p class="small text-muted mb-4">Paste these placeholders into your template.</p>
                                
                                <div class="tag-group mb-4">
                                    <div class="d-flex flex-wrap gap-2">
                                        <code class="tag-item-success">{{visitor_name}}</code>
                                        <code class="tag-item-success">{{company_name}}</code>
                                        <code class="tag-item-success">{{pass_no}}</code>
                                        <code class="tag-item-success">{{purpose}}</code>
                                        <code class="tag-item-success">{{in_time}}</code>
                                        <code class="tag-item-success">{{emp_name}}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_template" class="btn btn-success px-5 rounded-3 shadow">
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
    .tag-item { background: white; padding: 4px 10px; border-radius: 6px; color: #4361ee; border: 1px solid rgba(67,97,238,0.1); cursor: pointer; transition: 0.2s; }
    .tag-item:hover { background: #4361ee; color: white; }
    .tag-item-success { background: white; padding: 4px 10px; border-radius: 6px; color: #198754; border: 1px solid rgba(25,135,84,0.1); }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const editModal = new bootstrap.Modal(document.getElementById('editTemplateModal'));
    
    $('.editTemplateBtn').on('click', function() {
        const btn = $(this);
        $('#edit_template_id').val(btn.data('id'));
        $('#edit_template_name').val(btn.data('name'));
        $('#edit_from_email').val(btn.data('from'));
        $('#edit_subject').val(btn.data('subject'));
        $('#edit_cc_email').val(btn.data('cc'));
        $('#edit_body').val(btn.data('body'));
        $('#edit_mail_footer').val(btn.data('footer'));
        editModal.show();
    });

    // Copy tag to clipboard on click
    $('.tag-item').on('click', function() {
        const text = $(this).text();
        navigator.clipboard.writeText(text);
        const originalText = $(this).text();
        $(this).text('Copied!');
        setTimeout(() => $(this).text(originalText), 1000);
    });
});
</script>

<?php include("includes/footer.php"); ?>
