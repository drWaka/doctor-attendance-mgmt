<?php 
  require_once 'include/_autoload.php';
?>
<!doctype html>
<html lang="en">
  <head>
  <?php require_once 'include/page-part/head.inc.php'; ?>
  </head>
  <body>
    <div class="app">
        <div class="app-body">

            <?php require_once 'include/page-part/side-nav.inc.php'; ?>

            <?php require_once 'include/page-part/header.inc.php'; ?>

            <div class="container-fluid">

                <div class="row margin-top-lg">
                    <div class="col-6 offset-3">
                    <div class="row text-center" style="background-color: #FFF; border-radius: 10px">
                        <div class="col-sm-12 margin-top">
                        <img src="../core/img/ollh-logo.gif" alt="" style="width : 60%">
                        </div>
                        <div class="col-sm-12 margin-top-sm margin-bottom-sm">
                        <h2 class="uppercase">E-Survey System</h2>
                        <h3>Our Lady Of Lourdes Hospital</h3>
                        </div>
                    </div>
                    </div>
                </div>

            </div>

        </div>
    </div>    

    <div class="modal-container"></div>
    
    <?php require_once 'include/page-part/js-script.inc.php'; ?>
  </body>
</html>