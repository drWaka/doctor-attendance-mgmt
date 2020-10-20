<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => ''
    ),
    "contentType" => 'modal'
);

if (isset($_POST['divisionId'])) {
    $divisionId = new form_validation($_POST['divisionId'], 'str-int', 'Division ID', true);

    $division = '';
    if ($divisionId -> valid == 1) {
        // Verify if the Employee ID is valid
        if ($divisionId -> value == 'new-rec') {
            $division = array(
                "description" => ''
            );
        } else {
            $division = MscDivision::show($divisionId -> value);
            // die(var_dump($employee));
            if (is_null($division)) {
                $divisionId -> valid = 0;
                $divisionId -> err_msg = "Division Record not found";
            }
        }
    }

    if ($divisionId -> valid == 1) {
        $response['content']['modal'] = modalize(
            '<div class="row">
                <div class="col-sm-12">
                <h2 class="header capitalize text-center">Division Management</h2>
                <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                </div>
                
                <div class="col-sm-12 item-guide-mgmt">
                    <form form-name="division-form" action="../core/ajax/division-manage.php" tran-type="async-form">
                        <input type="text" name="divisionId" hidden="hidden" value="' . $divisionId -> value . '">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Division Name: </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="description" placeholder="Division Name" value="' . $division['description'] . '">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>', 
            array(
                "trasnType" => 'regular',
                "btnLbl" => 'Submit'
            )
        );
    } else {
        $response['success'] = 'failed';
        $response['contentType'] = 'modal';
        $response['content']['modal'] = modalize(
            "<div class='row text-center'>
                <h2 class='header capitalize col-12'>System Error Encountered</h2>
                <p class='para-text col-12'>Error Details: {$divisionId -> err_msg}</p>
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