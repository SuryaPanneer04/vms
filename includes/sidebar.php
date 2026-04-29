<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'];
$user_name = $_SESSION['emp_name'] ?? 'User';
$user_email = $_SESSION['email'] ?? '';
$is_admin = ($user_role == 'admin');
$is_manager = ($_SESSION['designation'] == 'Manager');

// Fetch Dynamic Sidebar Color
$stmt_theme = $con->prepare("SELECT setting_value FROM app_settings WHERE setting_key = 'sidebar_color'");
$stmt_theme->execute();
$sidebar_color = $stmt_theme->fetchColumn() ?: '#000000';
?>

<div class="sidebar d-flex flex-column" id="sidebar" style="background-color: <?= $sidebar_color ?> !important;">
    <div class="sidebar-header px-3 pt-4 mb-2 d-flex flex-column">
        <div class="d-flex align-items-center mb-4 px-2">
            <div class="bg-primary rounded-3 p-2 me-2 shadow-sm">
                <i class="fas fa-shield-halved text-white fs-5"></i>
            </div>
            <h4 class="text-white fw-bold mb-0 ls-1">VMS <span class="text-primary">PRO</span></h4>
            <button class="btn btn-link text-white d-xl-none ms-auto p-0" onclick="toggleSidebar()">
                <i class="fas fa-times fs-4"></i>
            </button>
        </div>
        
        <div class="user-profile-card p-3 rounded-4 mb-3">
            <div class="d-flex align-items-center mb-2">
                <div class="avatar-circle me-3 flex-shrink-0">
                    <?= strtoupper(substr($user_name, 0, 1)) ?>
                </div>
                <div class="overflow-hidden w-100">
                    <h6 class="text-white fw-bold mb-0 text-truncate" style="font-size: 0.85rem;" title="<?= htmlspecialchars($user_name) ?>"><?= htmlspecialchars($user_name) ?></h6>
                    <div class="badge bg-primary-subtle text-primary border-primary mt-1 px-2" style="font-size: 0.55rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?= htmlspecialchars($user_role) ?>
                    </div>
                </div>
            </div>
            <div class="mt-2 pt-2 border-top border-white border-opacity-10 small text-white text-truncate" style="font-size: 0.65rem; opacity: 0.8;" title="<?= htmlspecialchars($user_email) ?>">
                <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($user_email) ?>
            </div>
        </div>
    </div>

    <div class="sidebar-content">
        <ul class="nav flex-column px-2">
        <?php if ($user_role == 'admin'): ?>     
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($user_role == 'admin'): ?>
            <!-- Master Data Dropdown -->
            <?php 
                $master_pages = ['departments.php', 'designations.php', 'add_Locations.php'];
                $is_master_active = in_array($current_page, $master_pages);
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $is_master_active ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#masterMenu" role="button" aria-expanded="<?= $is_master_active ? 'true' : 'false' ?>">
                    <i class="fas fa-layer-group"></i> <span>Master</span>
                    <i class="fas fa-chevron-down ms-auto transition-icon" style="font-size: 0.7rem;"></i>
                </a>
                <div class="collapse <?= $is_master_active ? 'show' : '' ?>" id="masterMenu">
                    <ul class="nav flex-column ps-4 mb-2">
                        <li class="nav-item">
                            <a href="departments.php" class="nav-link py-1 <?= $current_page == 'departments.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                                <i class="fas fa-building me-2" style="width: 15px;"></i> Departments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="designations.php" class="nav-link py-1 <?= $current_page == 'designations.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                                <i class="fas fa-user-tag me-2" style="width: 15px;"></i> Designations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="add_Locations.php" class="nav-link py-1 <?= $current_page == 'add_Locations.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                                <i class="fas fa-industry me-2" style="width: 15px;"></i> Locations
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Org Tools Dropdown -->
            <?php 
                $user_pages = ['manage_users.php', 'staff_list.php', 'employee_import.php', 'reporting_chart.php'];
                $is_user_active = in_array($current_page, $user_pages);
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $is_user_active ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#userMenu" role="button" aria-expanded="<?= $is_user_active ? 'true' : 'false' ?>">
                    <i class="fas fa-users-cog"></i> <span>Organizational Tools</span>
                    <i class="fas fa-chevron-down ms-auto transition-icon" style="font-size: 0.7rem;"></i>
                </a>
                <div class="collapse <?= $is_user_active ? 'show' : '' ?>" id="userMenu">
                    <ul class="nav flex-column ps-4 mb-2">
                        <li class="nav-item">
                            <a href="manage_users.php" class="nav-link py-1 <?= $current_page == 'manage_users.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                                <i class="fas fa-user-check me-2" style="width: 15px;"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_list.php" class="nav-link py-1 <?= $current_page == 'staff_list.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                                <i class="fas fa-list-ul me-2" style="width: 15px;"></i> Staff List
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="employee_import.php" class="nav-link py-1 <?= $current_page == 'employee_import.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                                <i class="fas fa-file-import me-2" style="width: 15px;"></i> Import Data
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reporting_chart.php" class="nav-link py-1 <?= $current_page == 'reporting_chart.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                                <i class="fas fa-sitemap me-2" style="width: 15px;"></i> Reporting Hierarchy
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <div class="section-title mt-2">Visitor Management</div>
            <li class="nav-item">
                <a href="employee_portal.php" class="nav-link <?= $current_page == 'employee_portal.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-clock"></i> <span>Visitor Portal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="list_visitor.php" class="nav-link <?= $current_page == 'list_visitor.php' ? 'active' : '' ?>">
                    <i class="fas fa-address-book"></i> <span>Master Logs</span>
                </a>
            </li>

        <?php elseif ($user_role == 'employee'): ?>
            <li class="nav-item">
                <a href="employee_portal.php" class="nav-link <?= $current_page == 'employee_portal.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-clock"></i> <span>My Visitors</span>
                </a>
            </li>

            <?php if ($is_manager): ?>
                <li class="nav-item">
                    <a href="reporting_chart.php" class="nav-link <?= $current_page == 'reporting_chart.php' ? 'active' : '' ?>">
                        <i class="fas fa-sitemap"></i> Team Insights
                    </a>
                </li>
            <?php endif; ?>

        <?php elseif ($user_role == 'gate' || $user_role == 'timeoffice'): ?>
            <div class="section-title">Security Entry</div>
             <li class="nav-item">
                <a href="add_visitor_form.php" class="nav-link <?= $current_page == 'add_visitor_form.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-plus"></i> Visitor Entry
                </a>
            </li>
            <li class="nav-item">
                <a href="list_visitor.php" class="nav-link <?= $current_page == 'list_visitor.php' ? 'active' : '' ?>">
                    <i class="fas fa-address-book"></i> Entry Logs
                </a>
            </li>
        <?php endif; ?>

        <!-- Settings Dropdown -->
        <?php 
            $settings_pages = ['change_password.php', 'mail_config.php', 'theme_settings.php'];
            $is_settings_active = in_array($current_page, $settings_pages);
        ?>
        <li class="nav-item">
            <a class="nav-link <?= $is_settings_active ? '' : 'collapsed' ?>" data-bs-toggle="collapse" href="#settingsMenu" role="button" aria-expanded="<?= $is_settings_active ? 'true' : 'false' ?>">
                <i class="fas fa-cog"></i> <span>Settings</span>
                <i class="fas fa-chevron-down ms-auto transition-icon" style="font-size: 0.7rem;"></i>
            </a>
            <div class="collapse <?= $is_settings_active ? 'show' : '' ?>" id="settingsMenu">
                <ul class="nav flex-column ps-4 mb-2">
                    <li class="nav-item">
                        <a href="change_password.php" class="nav-link py-1 <?= $current_page == 'change_password.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                            <i class="fas fa-key me-2" style="width: 15px;"></i> Change Password
                        </a>
                    </li>
                    <?php if ($user_role == 'admin'): ?>
                    <li class="nav-item">
                        <a href="mail_config.php" class="nav-link py-1 <?= $current_page == 'mail_config.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                            <i class="fas fa-envelope-open-text me-2" style="width: 15px;"></i> Mail Configuration
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="mail_templates.php" class="nav-link py-1 <?= $current_page == 'mail_templates.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                            <i class="fas fa-file-invoice me-2" style="width: 15px;"></i> Mail Templates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="theme_settings.php" class="nav-link py-1 <?= $current_page == 'theme_settings.php' ? 'active' : '' ?>" style="font-size: 0.85rem;">
                            <i class="fas fa-palette me-2" style="width: 15px;"></i> Color Themes
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div> 
        </li>

        <div class="mt-auto mb-4 p-2">
            <li class="nav-item">
                <a href="logout.php" class="nav-link text-danger border border-danger-subtle rounded-3">
                    <i class="fas fa-power-off"></i> Sign Out
                </a>
            </li>
        </div>
    </div>
</div>

<style>
.user-profile-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); }
.avatar-circle {
    width: 42px; height: 42px; background: linear-gradient(135deg, #4361ee, #4cc9f0);
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 700; font-size: 1.1rem; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
}
.ls-1 { letter-spacing: 1px; }
.transition-icon { transition: transform 0.3s ease; }
.nav-link:not(.collapsed) .transition-icon { transform: rotate(180deg); }
.nav-link.active { background: rgba(67, 97, 238, 0.15) !important; border-right: 3px solid #4361ee; color: #4361ee !important; font-weight: 600; }
.nav-link i { width: 20px; text-align: center; }
.section-title { color: rgba(255,255,255,0.4); font-size: 0.65rem; font-weight: 800; padding: 15px 20px 5px; text-transform: uppercase; letter-spacing: 1px; }
</style>