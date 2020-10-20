<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => '',
        "record" => '',
        "total" => 0
    ),
    "contentType" => ''
);

if (isset($_POST['departmentId'])) {
    $departmentId = new form_validation($_POST['departmentId'], 'str-int', 'Department ID', true);

    if ($departmentId -> valid == 1) {
        // Determine if the Division Id exists
        if ($departmentId -> value != 'all' && is_numeric($departmentId -> value)) {
            $division = MscDepartment::show($departmentId -> value);

            if (!(count($division) > 0)) {
                $departmentId -> valid = 0;
                $departmentId -> err_msg = 'Department ID is invalid';
            }
        }
    }

    if ($departmentId -> valid == 1) {
        $response['contentType'] = 'dynamic-content';
        $response['content']['form'] = "<option value='all' style='display:none'>Choose a Unit</option>";

        if ($departmentId -> value != 'all' && is_numeric($departmentId -> value)) {
            $units = MscUnit::getByDepartment($departmentId -> value);
            if (count($units) > 0) {
                foreach($units as $unit) {
                    $response['content']['form'] .= "<option value='{$unit['PK_mscUnit']}'>{$unit['description']}</option>";
                }
            } else {
                $response['content']['form'] .= "<option value='0'>No Unit</option>";
            }
        }

    } else {
        $errorMessage = '';
        if ($departmentId -> valid == 0) {
            $errorMessage = $departmentId -> err_msg;
        }

        $response['success'] = 'failed';
        $response['contentType'] = 'modal';
        $response['content']['modal'] = modalize(
            "<div class='row text-center'>
                <h2 class='header capitalize col-12'>System Error Encountered</h2>
                <p class='para-text col-12'>Error Details: {$errorMessage}</p>
            </div>", 
            array(
                "trasnType" => 'error',
                "btnLbl" => 'Dismiss'
            )
        );  
    }

} else {
    $response['success'] = 'failed';
    $response['contentType'] = 'modal';
    $response['content']['modal'] = modalize(
        '<div class="row text-center">
            <h2 class="header capitalize col-12">System Error Encountered</h2>
            <p class="para-text col-12">Error Details: Insufficient Data Submitted<br/> Please contact your System Administrator</p>
        </div>', 
        array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
        )
    );   
}


// Encode JSON Response
encode_json_file($response);