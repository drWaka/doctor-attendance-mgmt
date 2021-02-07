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

    /* Loading CSS */
    .loading-cover {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #0004;
      z-index: 1031;
    }
    .loading-cover .load-icon {  
      position: fixed;
      top: 30vh;
      left: 45%;
      width: 200px;
      height: 200px;
    }

    .loading-cover.show {
      display: block;
    }
  </style>
  </head>
  <body>
    <div class="loading-cover">
      <img class="load-icon" src="../core/img/load-icon.svg" alt="loading icon">
    </div>
    <div class="app">
        <div class="app-body">

            <?php require_once 'include/page-part/side-nav.inc.php'; ?>

            <?php require_once 'include/page-part/header.inc.php'; ?>

            <?php 
                $basename = basename($_SERVER['PHP_SELF'], '.php');
                if (isset($_GET['pageName']) && !empty($_GET['pageName'])) {
                    // ToDo: Create at script that will redirect to 404 Page
                    // $filePath = "include/pages/{$basename}/{$_GET['pageName']}.php";
                    // if (!file_exists($filePath)) {
                    //   header('Location: error404.php');
                    // }

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
      var pageConfig = {
        limit : 5,
        total : 1,
        page : 1
      };

      $(document).ready(function() {
        loadRecord();
        // Load Report Form
        console.log(typeof loadReportForm);
        if (typeof loadReportForm == "function") {
          loadReportForm();
        }

        $(document).on('click', '.filter-toggle', function() {
          let advanceFilter = document.querySelector('.advance-filter');

          if (advanceFilter.className.indexOf('hide') > -1) {
            advanceFilter.classList.remove('hide');
          } else {
            advanceFilter.classList.add('hide');
          }
        });

        $(document).on('change', '.form-control.filter', function() {
          pageConfig['page'] = 1;
          loadRecord();
          // Load Report Form
          if (typeof loadReportForm == "function") {
            loadReportForm();
          }
        });

        $(document).on('keyup', '.form-control.filter-search', function() {
          pageConfig['page'] = 1;
          loadRecord();
          // Load Report Form
          if (typeof loadReportForm == "function") {
            loadReportForm();
          }
        });
        // For Search Box that has Search Button
        $(document).on('click', '.filter-search-btn', function() {
          pageConfig['page'] = 1;
          loadRecord();
          // Load Report Form
          if (typeof loadReportForm == "function") {
            loadReportForm();
          }
        });
      });
    </script>
  </body>
</html>