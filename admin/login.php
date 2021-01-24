<?php 
  require_once 'include/_autoload.php';

  // Check if the $_SESSION['userId'] exists
  if (isset($_SESSION['userId'])) {
    // header('Location: html/index.php');
  }

  // Error Management Section
  $userIdErr = $userPasswordErr = '';
  if (isset($_SESSION['UserIdErr'])) {
    $userIdErr = $_SESSION['UserIdErr'];
    // Unset the Used Session Variables
    unset($_SESSION['UserIdErr']);
  }
  $userIdErr = new error_handler($userIdErr);

  if (isset($_SESSION['PasswordErr'])) {
    $userPasswordErr = $_SESSION['PasswordErr'];
    // Unset the Used Session Variables
    unset($_SESSION['PasswordErr']);
  }
  $userPasswordErr = new error_handler($userPasswordErr);

  // Field Values
  $userIdValue = '';
  if (isset($_SESSION['UserIdValue'])) {
    $userIdValue = $_SESSION['UserIdValue'];
    unset($_SESSION['UserIdValue']);
  }

?>
<!doctype html>
<html lang="en">
  <head>
  <?php require_once 'include/page-part/head.inc.php'; ?>
  </head>
  <body>
    <img src="../core/img/login-bg.jpg" id="page-bg" alt="">
    <div class="bg-backrop"></div>

    <div class="login-form">
      <div class="row login-header">
        <div class="col-12 text-center">
          <img src="../core/img/ollh-logo.gif" alt="">
          <h2><?= $_ENV['APP_NAME'] ?><br><small>Our Lady Of Lourdes Hospital</small></h2>
        </div>
      </div>
      <form class="" action="../core/requests/admin-login.php" method="post" submit-type="synchronous" form-name="admin-login">

        <div class="col-md-12">
          <label for="">User ID : </label>
          <div class="form-group">
            <input type="text" name="userId" placeholder="User ID" class="form-control form-control-line <?=$userIdErr -> error_class?>" value="<?=$userIdValue?>" autocomplete="off">
            <?=$userIdErr -> error_icon?>
            <?=$userIdErr -> error_text?>
          </div>
        </div>

        <div class="col-md-12">
          <label for="">Password : </label>
          <div class="form-group">
            <input type="password" name="password" placeholder="Password" class="form-control form-control-line <?=$userPasswordErr -> error_class?>">
            <?=$userPasswordErr -> error_icon?>
            <?=$userPasswordErr -> error_text?>
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group text-center">
            <button class="btn btn-info form-submit-button" type="button">LOGIN</button>
          </div>
        </div>

        <div class="">
          <div class="col-sm-12 text-center">
            <!-- <button type="button" class="btn btn-link transaction-btn" data-content="{}" data-link="asset/core/ajax/login-reset-password-select.php" trans-name="modal-rec">Forgot Your Password?</button> -->
          </div>
        </div>

      </form>
    </div>

    <div class="modal-container"></div>
    
    <?php require_once 'include/page-part/js-script.inc.php'; ?>

    <!-- User Defined Script -->
    <script>
      $(document).ready(function() {
        initialize_form_validation('.login-form');
      });
    </script>
  </body>
</html>