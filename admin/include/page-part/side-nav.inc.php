<!-- Sidebar Start -->
<div class="app-sidebar">
  <div class="text-right">
    <button type="button" class="btn btn-sidebar" data-dismiss="sidebar"><span class="x"></span>
    </button>
  </div>
  
  <div class="sidebar-header">
    <img src="../core/img/ollh-logo.gif" class="user-photo">
    <p class="username"><?= $_ENV['APP_NAME'] ?><br><small>Our Lady Of Lourdes Hospital</small></p>
  </div>

  <ul id="sidebar-nav" class="sidebar-nav">
    <li class="sidebar-nav-group <?= $_SESSION['userType'] . ' ' . isHidden($_SESSION['userType'], ['administrator', 'hrod'])?>">
        <a href="#attendance-mgmt" class="sidebar-nav-link" data-toggle="collapse">
          <i class="fas fa-clock"></i> Attendance Mgmt.
        </a>
        <ul id="attendance-mgmt" class="collapse" data-parent="#sidebar-nav">
            <li><a href="attendance-mgmt.php?pageName=doctor-logs" class="sidebar-nav-link">Doctor Attendance</a></li>
            <li><a href="attendance-mgmt.php?pageName=logs-validation" class="sidebar-nav-link">Attendance Validation</a></li>
        </ul>
    </li>

    <li class="sidebar-nav-group <?=isHidden($_SESSION['userType'], ['administrator', 'hrod']) ?>">
        <a href="#respondent-mgmt" class="sidebar-nav-link" data-toggle="collapse">
            <i class="fa fa-user"></i> Doctor Mgmt.
        </a>
        <ul id="respondent-mgmt" class="collapse" data-parent="#sidebar-nav">
            <li><a href="doctor.php?pageName=doctor" class="sidebar-nav-link">Doctor</a></li>
            <li><a href="doctor.php?pageName=department" class="sidebar-nav-link">Department</a></li>
        </ul>
    </li>
    
    <li class="sidebar-nav <?=isHidden($_SESSION['userType'], ['administrator']) ?>">
        <a href="user-mgmt.php?pageName=users" class="sidebar-nav-link">
          <i class="fas fa-user-plus"></i> User Management
        </a>
    </li>
  </ul>

  <div class="sidebar-footer">
  <a href="#" data-toggle="tooltip" 
      class="transaction-btn"
      trans-name='async-form'
      data-link='../core/ajax/user-mgmt-user-select.php'
      data-content='{
          &quot;userMstrId&quot; : &quot;<?=$_SESSION['userId'] ?>&quot;
      }'
      data-target='.modal-container'
      title="Edit My Profile"
    >
      <i class="icon-user"></i> 
    </a>
    <a href="#" data-toggle="tooltip" class="transaction-btn" 
      trans-name='async-form'
      data-link='../core/ajax/user-mgmt-user-password-select.php'
      data-content='{
          &quot;userMstrId&quot; : &quot;<?=$_SESSION['userId'] ?>&quot;,
          &quot;tranType&quot; : &quot;userReset&quot;
      }'
      data-target='.modal-container'
      title='Change My Password'
    >
      <i class="icon-key"></i> 
    </a>
    <a href="../core/requests/admin-logout.php" data-toggle="tooltip" title="Logout">
      <i class="icon-logout"></i>
    </a>
  </div>
</div>
<!-- Sidebar End -->
