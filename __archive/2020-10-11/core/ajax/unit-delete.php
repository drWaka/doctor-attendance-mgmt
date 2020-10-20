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

  if (isset($_POST['recordId'])) {
    $recordId = new form_validation($_POST['recordId'], 'int', 'Department Record ID', true);

    if ($recordId -> valid == 1) {
      // Check if still being reference by a employee record
      $employee = Employee::getByUnit($recordId -> value);
      if (is_array($employee)) {
        if (count($employee) > 0) {
          $recordId -> valid = 0;
          $recordId -> err_msg = 'Unit is still being referenced by an employee record';
        }
      }
    } 

    if ($recordId -> valid == 1) {
      if (is_numeric($recordId -> value)) {
        $division = MscUnit::show($recordId -> value);
        if (is_null($division)) {
          $recordId -> valid = 0;
          $recordId -> err_msg = 'Unit Record Not Found';
        }
      }
    }

    if ($recordId -> valid == 1) {
      $modalLbl = array(
        "present" => 'Delete',
        "past" => 'Deleted',
        "future" => 'Delete'
      );

      $isDeleteSuccess = MscUnit::delete($recordId -> value);
      if ($isDeleteSuccess) {
        $response['content']['modal'] = modalize( 
          '<div class="row text-center">
            <div class="col-sm-12">
            <h2 class="header capitalize">Unit ' . $modalLbl['present'] . ' Success</h2>
            <p class="para-text">Unit ' . $modalLbl['past'] . ' Successfully</p>
            </div>
          </div>', 
          array(
            "trasnType" => 'btn-trigger',
            "btnLbl" => 'OK',
          )
        );
      } else {
        $response['content']['modal'] = modalize( 
          '<div class="row text-center">
              <h2 class="header capitalize col-12">Error Encountered</h2>
              <p class="para-text col-12">Error Details: Unable to ' . $modalLbl['future'] . ' Unit Record</p>
          </div>', 
          array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
          )
        );
      }
    } else {
      if ($recordId -> valid == 0) {
        $response['content']['modal'] = modalize( 
          '<div class="row text-center">
              <h2 class="header capitalize col-12">Error Encountered</h2>
              <p class="para-text col-12">Error Details: ' . $recordId -> err_msg . '</p>
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
  // die(var_dump($response));
  encode_json_file($response);
?>