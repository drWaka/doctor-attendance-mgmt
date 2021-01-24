<!-- Navbar Start -->
<nav class="navbar navbar-expand navbar-light bg-white">
  <button type="button" class="btn btn-sidebar" data-toggle="sidebar"><i class="icon-menu"></i></button>
  <div class="navbar-brand">OLLH &minus; <?= $_ENV['APP_SHORT_NAME'] ?></div>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a href="#" class="nav-link dropdown-toggle capitalize" data-toggle="dropdown">
        <?= substr($_SESSION['firstName'], 0, 1) . '. ' . $_SESSION['lastName'] ?>
          
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="#" class="dropdown-item transaction-btn"
          trans-name='async-form'
          data-link='../core/ajax/user-mgmt-user-select.php'
          data-content='{
              &quot;userMstrId&quot; : &quot;<?=$_SESSION['userId'] ?>&quot;
          }'
          data-target='modal-container'
          title="Edit My Profile"
        >
          <div>My Profile</div>
        </a>
        <div class="dropdown-divider"></div>
        <a href="#" class="transaction-btn dropdown-item" trans-name='async-form'
          data-link='../core/ajax/user-mgmt-user-password-select.php'
          data-content='{
              &quot;userMstrId&quot; : &quot;<?=$_SESSION['userId'] ?>&quot;,
              &quot;tranType&quot; : &quot;userReset&quot;
          }'
          data-target='modal-container'
          title='Change My Password'
        >
          <div>Change Password</div>
        </a>
        <div class="dropdown-divider"></div>
        <a href="../core/requests/admin-logout.php" class="dropdown-item">
          <div>Logout</div>
        </a>
      </div>
    </li>
  </ul>
</nav>
<!-- Navbar End -->