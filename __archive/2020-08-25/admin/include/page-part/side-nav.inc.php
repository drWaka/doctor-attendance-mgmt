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

    <li class="sidebar-nav-group">
        <a href="#respondent-mgmt" class="sidebar-nav-link" data-toggle="collapse">
            <i class="fa fa-user"></i> Respondent Mgmt.
        </a>
        <ul id="respondent-mgmt" class="collapse" data-parent="#sidebar-nav">
            <li><a href="respondents.php?pageName=respondents" class="sidebar-nav-link">Respondents</a></li>
            <li><a href="respondents.php?pageName=division" class="sidebar-nav-link">Division</a></li>
            <li><a href="respondents.php?pageName=department" class="sidebar-nav-link">Department</a></li>
            <li><a href="respondents.php?pageName=unit" class="sidebar-nav-link">Unit</a></li>
        </ul>
    </li>
    
    <li class="sidebar-nav">
        <a href="user-mgmt.php?pageName=users" class="sidebar-nav-link">
            <i class="fa fa-user"></i> User Management
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
      data-target='modal-container'
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
      data-target='modal-container'
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
