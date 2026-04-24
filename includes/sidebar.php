<?php

?>

<div class="sidebar">
   <ul class="nav flex-column mt-4">
      
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>
        

       
        <li class="nav-item">
            <a href="add_visitor_form.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_visitor_form.php' ? 'active' : '' ?>">
                <i class="fas fa-user-plus"></i> Add Visitor
            </a>
        </li>
        

        
        <li class="nav-item">
            <a href="list_visitor.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'list_visitor.php' ? 'active' : '' ?>">
                <i class="fas fa-list-ul"></i> Visitor List
            </a>
        </li>
       

       
        <li class="nav-item mt-3">
            <div class="px-3 small text-white-50 text-uppercase fw-bold">Administration</div>
        </li>
        <li class="nav-item">
            <a href="employee_portal.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'employee_portal.php' ? 'active' : '' ?>">
                <i class="fas fa-users-cog"></i> Employee portal
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_list.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'employee_list.php' ? 'active' : '' ?>">
                <i class="fas fa-users-cog"></i> Employee List
            </a>
        </li>
       

        
    </ul>
</div>