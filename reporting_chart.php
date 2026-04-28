<?php
include("includes/config.php");

// Only logged in users can access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// AJAX Handler for subordinates
if (isset($_GET['get_subordinates'])) {
    $manager_id = $_GET['get_subordinates'];
    if ($user_role == 'admin' && $manager_id == 'root') {
        $stmt = $con->prepare("SELECT id, full_name, designation, department, email, reporting_manager FROM users WHERE reporting_manager = 0");
        $stmt->execute();
    } else {
        $stmt = $con->prepare("SELECT id, full_name, designation, department, email, reporting_manager FROM users WHERE reporting_manager = ?");
        $stmt->execute([$manager_id]);
    }
    $subordinates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($subordinates as &$sub) {
        $check = $con->prepare("SELECT COUNT(*) FROM users WHERE reporting_manager = ?");
        $check->execute([$sub['id']]);
        $sub['has_subordinates'] = $check->fetchColumn() > 0;
    }
    header('Content-Type: application/json');
    echo json_encode($subordinates);
    exit();
}

// AJAX Handler for visitor list
if (isset($_GET['get_visitor_list'])) {
    $emp_id = $_GET['get_visitor_list'];
    $stmt = $con->prepare("
        SELECT h.*, v.visitor_name, v.pass_no, v.company_name, v.purpose,
               u_from.full_name as assigned_from,
               (SELECT u_to.full_name FROM visitor_handoffs h_next 
                JOIN users u_to ON h_next.emp_id = u_to.id 
                WHERE h_next.visitor_id = h.visitor_id AND h_next.id > h.id 
                ORDER BY h_next.id ASC LIMIT 1) as assigned_to
        FROM visitor_handoffs h
        JOIN visitor_master v ON h.visitor_id = v.id
        LEFT JOIN users u_from ON h.assigned_by = u_from.id
        WHERE h.emp_id = ?
        ORDER BY h.id DESC
    ");
    $stmt->execute([$emp_id]);
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($visitors);
    exit();
}

include("includes/header.php");
include("includes/sidebar.php");
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">

<style>
    .content-area {
        margin-left: 260px;
        padding: 0;
        background: #f8faff;
        min-height: 100vh;
        width: calc(100% - 260px);
        flex-grow: 1;
    }
    
    @media (max-width: 992px) {
        .content-area { margin-left: 0; width: 100%; padding: 15px; padding-top: 80px; }
    }

    /* Top Header Responsiveness */
    .header-bar {
        background: white; border-bottom: 1px solid #edf2f9; padding: 15px 30px; 
        display: flex; align-items: center; justify-content: space-between; gap: 15px;
    }
    
    @media (max-width: 992px) {
        .header-bar { flex-direction: column; align-items: flex-start; padding: 15px; }
        .filter-group { width: 100%; display: flex; flex-direction: column; gap: 10px; }
        .filter-group input, .filter-group select, .filter-group button { width: 100% !important; }
    }

    .hierarchy-card { background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); height: 100%; }
    
    .tree-scroll { max-height: 500px; overflow-y: auto; padding-right: 10px; }
    
    @media (min-width: 992px) {
        .tree-scroll { max-height: calc(100vh - 250px); }
    }

    /* Tree Styles */
    .tree-branch { list-style: none; padding-left: 20px; position: relative; margin-top: 5px; }
    .tree-branch::before { content: ''; position: absolute; top: 0; left: 8px; background: #dee2e6; width: 2px; height: 100%; }
    .tree-node { position: relative; margin-bottom: 5px; }
    .tree-node::before { content: ''; position: absolute; top: 18px; left: -12px; background: #dee2e6; width: 12px; height: 2px; }
    
    .employee-card {
        padding: 8px 12px; border: 1px solid #eef2f7; border-radius: 8px; display: flex; align-items: center; 
        background: white; cursor: pointer; transition: all 0.2s;
    }
    .employee-card:hover { border-color: #4361ee; background: #f8faff; }
    .employee-card.active { background: #4361ee; color: white; }
    .employee-card.active .text-muted { color: rgba(255,255,255,0.7) !important; }
    .employee-card.active .avatar { background: rgba(255,255,255,0.2); border-color: transparent; }

    .avatar {
        width: 34px; height: 34px; background: #4361ee; color: white; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-weight: 700;
        font-size: 0.8rem; margin-right: 10px; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .expand-icon { margin-left: auto; color: #ced4da; transition: transform 0.2s; }
    .employee-card.expanded .expand-icon { transform: rotate(45deg); color: #ef476f; }

    /* Right Panel List */
    .dt-buttons { margin-bottom: 15px; }
    .badge-time { font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; font-weight: 600; }
</style>

<div class="content-area p-0">
    <!-- Top Header -->
    <div class="header-bar shadow-sm">
        <div>
            <h4 class="fw-bold text-dark mb-0">Reporting Hierarchy</h4>
            <p class="text-muted small mb-0">Select an employee to view their log</p>
        </div>
        
        <div class="filter-group d-flex gap-2">
            <input type="text" id="nodeSearch" class="form-control form-control-sm border-0 bg-light" placeholder="Search..." style="width: 180px;">
            <select id="deptFilter" class="form-select form-select-sm border-0 bg-light" style="width: 140px;">
                <option value="">All Depts</option>
                <?php 
                $depts = $con->query("SELECT dept_name, dept_color FROM departments WHERE status=1")->fetchAll();
                foreach($depts as $d): ?>
                    <option value="<?= $d['dept_name'] ?>" style="background-color: <?= $d['dept_color'] ?>; color: #fff;">
                        <?= $d['dept_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary btn-sm px-4" onclick="filterTree()">Apply</button>
        </div>
    </div>

    <div class="p-4">
        <div class="row g-4">
        <!-- Left: Tree Content -->
        <div class="col-lg-4">
            <div class="hierarchy-card p-3 h-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-sitemap text-primary me-2"></i>
                    <h6 class="fw-bold mb-0">Organization Map</h6>
                </div>
                <div class="tree-scroll" id="org-tree-root">
                    <div class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div></div>
                </div>
            </div>
        </div>

        <!-- Right: Visitor Details -->
        <div class="col-lg-8">
            <div class="hierarchy-card p-4 h-100">
                <!-- Placeholder -->
                <div id="placeholderPane" class="text-center py-5">
                    <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-id-card fa-3x text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-muted">Select an Employee to View Log</h5>
                    <p class="text-muted small">Choose any member from the hierarchy tree on the left.</p>
                </div>

                <!-- Actual Content -->
                <div id="contentPane" class="d-none">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div id="activeAvatar" class="avatar" style="width: 50px; height: 50px; font-size: 1.2rem;"></div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-primary" id="activeName">Employee Name</h4>
                                    <div class="small">
                                        <span id="activeDesig" class="text-muted">Desig</span> • 
                                        <span id="activeDept" class="fw-bold text-dark">Dept</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="card bg-primary-subtle border-0 p-2 text-center">
                                <h4 class="fw-bold text-primary mb-0" id="totalVisitorCount">0</h4>
                                <small class="text-muted fw-bold">Total Visitors</small>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="visitorTable" class="table table-hover align-middle w-100">
                            <thead>
                                <tr class="table-light">
                                    <th>Visitor</th>
                                    <th>Company</th>
                                    <th class="text-center">Pass No</th>
                                    <th>Timing</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
let vTable;
const userId = <?= json_encode($user_id) ?>;
const userRole = <?= json_encode($user_role) ?>;

$(document).ready(function() {
    const targetId = (userRole === 'admin') ? 'root' : userId;
    fetchNodes(targetId, $('#org-tree-root'), true);
});

async function fetchNodes(managerId, container, isRoot = false) {
    try {
        const resp = await fetch(`reporting_chart.php?get_subordinates=${managerId}`);
        const data = await resp.json();
        if (isRoot) container.empty();

        if (data.length > 0) {
            const ul = $('<ul class="tree-branch"></ul>');
            if (isRoot) ul.css('padding-left', '0');
            data.forEach(user => {
                const li = $('<li class="tree-node"></li>');
                const initials = user.full_name.split(' ').map(n=>n[0]).join('').toUpperCase().substring(0,2);
                
                const card = $(`
                    <div class="employee-card" data-id="${user.id}" data-name="${user.full_name}" data-dept="${user.department}" data-desig="${user.designation}">
                        <div class="avatar">${initials}</div>
                        <div class="user-info w-100 overflow-hidden">
                            <p class="mb-0 fw-bold small text-truncate">${user.full_name}</p>
                            <p class="mb-0 text-muted" style="font-size:0.65rem;">${user.designation}</p>
                        </div>
                        ${user.has_subordinates ? '<i class="fas fa-plus-circle expand-icon small"></i>' : ''}
                    </div>
                `);

                li.append(card);
                ul.append(li);

                card.on('click', function(e) {
                    $('.employee-card').removeClass('active');
                    $(this).addClass('active');
                    loadVisitors(user);
                    if (user.has_subordinates && (e.target.closest('.expand-icon') || !$(this).hasClass('loaded'))) {
                        $(this).addClass('loaded');
                        toggleBranch($(this), li, user.id);
                    }
                });
            });
            container.append(ul);
        }
    } catch(e) {}
}

function toggleBranch(card, li, id) {
    if (card.hasClass('expanded')) {
        li.children('.tree-branch').slideUp(200);
        card.removeClass('expanded');
    } else {
        if (li.children('.tree-branch').length > 0) {
            li.children('.tree-branch').slideDown(200);
        } else {
            fetchNodes(id, li);
        }
        card.addClass('expanded');
    }
}

function loadVisitors(user) {
    $('#placeholderPane').addClass('d-none');
    $('#contentPane').removeClass('d-none');
    $('#activeName').text(user.full_name);
    $('#activeDept').text(user.department);
    $('#activeDesig').text(user.designation);
    $('#activeAvatar').text(user.full_name.split(' ').map(n=>n[0]).join('').toUpperCase().substring(0,2));

    if (vTable) vTable.destroy();
    
    vTable = $('#visitorTable').DataTable({
        ajax: { url: `reporting_chart.php?get_visitor_list=${user.id}`, dataSrc: '' },
        columns: [
            { data: 'visitor_name', render: (d,t,r) => `
                <div>
                    <strong class="text-primary">${d}</strong><br>
                    <small class='text-muted'>${r.purpose}</small>
                    <div class="mt-2 d-flex flex-column gap-1">
                        ${(r.assigned_from && r.assigned_from !== user.full_name) ? `
                        <div class="small py-1 px-2 bg-info-subtle text-info rounded-2" style="font-size:0.65rem;">
                            <i class="fas fa-long-arrow-alt-right me-1"></i>From: <strong>${r.assigned_from}</strong>
                        </div>` : ''}
                        ${r.assigned_to ? `
                        <div class="small py-1 px-2 bg-warning-subtle text-warning rounded-2" style="font-size:0.65rem;">
                            <i class="fas fa-share me-1"></i>Assigned To: <strong>${r.assigned_to}</strong>
                        </div>` : ''}
                    </div>
                </div>` 
            },
            { data: 'company_name' },
            { data: 'pass_no', className: 'text-center', render: d => `<span class='badge bg-light text-dark border fw-normal'>${d}</span>` },
            { data: null, render: r => {
                const inT = new Date(r.check_in_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                const outT = r.check_out_time ? new Date(r.check_out_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) : '<span class="text-success">Ongoing</span>';
                return `<div class='small'>In: ${inT}<br>Out: ${outT}</div>`;
            }},
            { data: null, render: r => {
                if(!r.check_out_time) return '-';
                let min = Math.round((new Date(r.check_out_time) - new Date(r.check_in_time))/60000);
                let durationTxt = "";
                if (min >= 60) {
                    let h = Math.floor(min / 60);
                    let m = min % 60;
                    durationTxt = h + " hr" + (h > 1 ? "s" : "") + (m > 0 ? " " + m + " mins" : "");
                } else {
                    durationTxt = min + " mins";
                }
                return `<span class='badge-time bg-light text-primary border'>${durationTxt}</span>`;
            }}
        ],
        order: [[0, 'desc']], // This might not work perfectly because index 0 is visitor_name. 
        // We should actually sort by a hidden date field or ID.
        // I'll add an order: [] to prevent DT from overriding the PHP order.
        aaSorting: [], 
        dom: "<'row'<'col-12'B>><'row mt-2'<'col-md-6'l><'col-md-6'f>>rt<'row'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm me-2', text: '<i class="fas fa-file-excel"></i> Excel' },
            { extend: 'print', className: 'btn btn-dark btn-sm', text: '<i class="fas fa-print"></i> Print' }
        ],
        drawCallback: function(s) { $('#totalVisitorCount').text(s.fnRecordsTotal()); },
        language: { emptyTable: "No visitor interactions found." }
    });
}

function filterTree() {
    const s = $('#nodeSearch').val().toLowerCase();
    const d = $('#deptFilter').val().toLowerCase();
    
    // Clear current visitor view as requested (organization show aganum entries venam)
    $('#placeholderPane').removeClass('d-none');
    $('#contentPane').addClass('d-none');
    $(".employee-card").removeClass("active");

    $('.employee-card').each(function() {
        const name = $(this).data('name').toLowerCase();
        const dept = $(this).data('dept').toLowerCase();
        $(this).closest('.tree-node').toggle((!s || name.includes(s)) && (!d || dept.includes(d)));
    });
}
</script>

<?php include("includes/footer.php"); ?>
