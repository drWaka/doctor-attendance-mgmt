<?php
  require '../php/_autoload.php';
  require '../model/_autoload.php';

  // JSON Variables
  $response = array(
    "success" => 'success',
    "content" => array(
      "modal" => ''
    ),
    "contentType" => 'modal'
  );

  if (isset($_POST['link']) && isset($_POST['dataContent']) && isset($_POST['headerTitle']) && isset($_POST['transType'])) {
    $link = new form_validation($_POST['link'], 'str-int', 'File Link', true);
    $dataContent = new form_validation(json_encode($_POST['dataContent']), 'str-int', 'Data Content', true);
    $headerTitle = new form_validation($_POST['headerTitle'], 'str-int', 'Header Title', true);
    $transType = new form_validation($_POST['transType'], 'str', 'Transaction Type', true);

    if ($link -> valid == 1 && $dataContent -> valid == 1 && $headerTitle -> valid == 1) {
      $response['content']['modal'] = modalize( 
        '<div class="row text-center">
            <h2 class="header capitalize col-12">' . $headerTitle -> value . ' Record ' . ucfirst($transType -> value) . '</h2>
            <p class="para-text col-12">Do you really want to ' . strtolower($transType -> value) . ' this record?</p>
        </div>', 
        array(
          "trasnType" => 'dialog',
          "btnLbl" => 'Yes',
          "btnLblClose" => 'No',
          "container" => '.modal-container',
          "link" => $link -> value,
          "transName" => 'async-form',
          "content" => $dataContent -> value
        )
      );
    } else {
      if ($link -> valid == 0) {
        $response['content']['modal'] = modalize( 
          '<div class="row text-center">
              <h2 class="header capitalize col-12">Error Encountered</h2>
              <p class="para-text col-12">Error Details: ' . $link -> err_msg . '</p>
          </div>', 
          array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
          )
        );
      } else if ($dataContent -> valid == 0) {
        $response['content']['modal'] = modalize( 
          '<div class="row text-center">
              <h2 class="header capitalize col-12">Error Encountered</h2>
              <p class="para-text col-12">Error Details: ' . $dataContent -> err_msg . '</p>
          </div>', 
          array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
          )
        );
      }  else if ($transType -> valid == 0) {
        $response['content']['modal'] = modalize( 
          '<div class="row text-center">
              <h2 class="header capitalize col-12">Error Encountered</h2>
              <p class="para-text col-12">Error Details: ' . $transType -> err_msg . '</p>
          </div>', 
          array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
          )
        );
      } else {
        $response['content']['modal'] = modalize( 
          '<div class="row text-center">
              <h2 class="header capitalize col-12">Error Encountered</h2>
              <p class="para-text col-12">Error Details: ' . $headerTitle -> err_msg . '</p>
          </div>', 
          array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
          )
        );
      }
    }
  } else {
    $response['content']['modal'] = modalize(
      '<div class="row text-center">
        <h2 class="header capitalize col-12">Error Encountered</h2>
        <p class="para-text col-12">Error Details: Insufficient Data Submitted</p>
      </div>', 
      array(
        "trasnType" => 'error',
        "btnLbl" => 'Dismiss'
      )
    );   
  }

  // Return JSON encode
  encode_json_file($response);
?>