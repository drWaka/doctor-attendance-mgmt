<!-- Sidebar Start -->
<div class="app-sidebar">
  <div class="text-right">
    <button type="button" class="btn btn-sidebar" data-dismiss="sidebar"><span class="x"></span>
    </button>
  </div>
  
  <div class="sidebar-header">
    <img src="../core/img/ollh-logo.gif" class="user-photo">
    <p class="username">E-Survey System<br><small>Our Lady Of Lourdes Hospital</small></p>
  </div>

  <ul id="sidebar-nav" class="sidebar-nav">
    <li class="sidebar-nav-group">
        <a href="#reports" class="sidebar-nav-link" data-toggle="collapse">
            <i class="fa fa-file"></i> Reports
        </a>
        <ul id="reports" class="collapse" data-parent="#sidebar-nav">
            <li><a href="reports.php?pageName=question-responses" class="sidebar-nav-link">User Responses</a></li>
        </ul>
    </li>

    <li class="sidebar-nav">
        <a href="#" class="sidebar-nav-link">
            <i class="fa fa-user"></i> User Management
        </a>
    </li>
  </ul>

  <div class="sidebar-footer">
    <a href="#" data-toggle="tooltip" class="transaction-btn" trans-name="modal-rec" data-target="modal-container" data-link="../asset/core/ajax/user-mgmt-select.php" data-content="{&quot;userId&quot; : &quot;<?= $_SESSION['userId'] ?>&quot;}" title="My Profile">
      <i class="icon-user"></i> 
    </a>
    <a href="#" data-toggle="tooltip" class="transaction-btn" trans-name="modal-rec" data-target="modal-container" data-link="../asset/core/ajax/password-mgmt-select.php" data-content="{&quot;userId&quot; : &quot;<?= $_SESSION['userId'] ?>&quot;}" title="Change Password">
      <i class="icon-key"></i> 
    </a>
    <a href="../asset/core/php/logout-script.php" data-toggle="tooltip" title="Logout">
      <i class="icon-logout"></i>
    </a>
  </div>
</div>
<!-- Sidebar End -->
