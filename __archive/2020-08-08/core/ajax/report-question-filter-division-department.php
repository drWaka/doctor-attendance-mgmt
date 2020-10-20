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

if (isset($_POST['divisionId'])) {
    $divisionId = new form_validation($_POST['divisionId'], 'str-int', 'Division ID', true);

    if ($divisionId -> valid == 1) {
        // Determine if the Division Id exists
        if ($divisionId -> value != 'all' && is_numeric($divisionId -> value)) {
            $division = MscDivision::show($divisionId -> value);

            if (!(count($division) > 0)) {
                $divisionId -> valid = 0;
                $divisionId -> err_msg = 'Division ID is invalid';
            }
        }
    }

    if ($divisionId -> valid == 1) {
        $response['contentType'] = 'dynamic-content';
        $response['content']['form'] = "<option value='all'>All Department</option>";

        if ($divisionId -> value != 'all' && is_numeric($divisionId -> value)) {
            $departments = MscDepartment::getByDivision($divisionId -> value);
            foreach($departments as $department) {
                $response['content']['form'] .= "<option value='{$department['PK_mscdepartment']}'>{$department['description']}</option>";
            }
        }

    } else {
        $errorMessage = '';
        if ($divisionId -> valid == 0) {
            $errorMessage = $divisionId -> err_msg;
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