<?php 
  require_once 'include/_autoload.php';
?>
<!doctype html>
<html lang="en">
  <head>
  <?php require_once 'include/page-part/head.inc.php'; ?>

  <style>
    .filter-fields {
      margin-top: 20px;
    }
    .advance-filter {
      padding: 15px;
      background-color: #FFF;
    }
    .advance-filter label {
      margin-top: 7px;
    }
  </style>
  </head>
  <body>
    <div class="app">
        <div class="app-body">

            <?php require_once 'include/page-part/side-nav.inc.php'; ?>

            <?php require_once 'include/page-part/header.inc.php'; ?>

            <?php 
                $basename = basename($_SERVER['PHP_SELF'], '.php');
                if (isset($_GET['pageName']) && !empty($_GET['pageName'])) {
                    require_once "include/pages/{$basename}/{$_GET['pageName']}.php";
                } else {
                    header('Location: homepage.php');
                }
            ?>

        </div>
    </div>    

    <div class="modal-container"></div>
    
    <?php require_once 'include/page-part/js-script.inc.php'; ?>

    <script>
      $(document).ready(function() {
        loadRecord();

        $(document).on('click', '.filter-toggle', function() {
          let advanceFilter = document.querySelector('.advance-filter');

          if (advanceFilter.className.indexOf('hide') > -1) {
            advanceFilter.classList.remove('hide');
          } else {
            advanceFilter.classList.add('hide');
          }
        });

      });
    </script>
  </body>
</html>