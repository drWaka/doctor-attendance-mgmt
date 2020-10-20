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

if (
    isset($_POST['divisionId']) && isset($_POST['description'])
) {
    $divisionId = new form_validation($_POST['divisionId'], 'str-int', 'Division ID', true);
    $description = new form_validation($_POST['description'], 'str-int', 'Division Name', true);

    $flags = array(
        'hasError' => 0,
        'isDone' => 0
    );

    if ($divisionId -> valid == 1 && $description -> valid == 1) {
        // Verify if the Employee ID is valid
        $employee = MscDivision::show($divisionId -> value);
        if (is_null($employee)) {
            if ($divisionId -> value !== 'new-rec') {
                $divisionId -> valid = 0;
                $divisionId -> err_msg = "Division Record not found";
            }
        } else {
            if (count(MscDivision::searchByName($description -> value, true))) {
                $divisionId -> valid = 0;
                $divisionId -> err_msg = "Division Record already exists";
            }
        }
    }

    if ($divisionId -> valid == 1 && $description -> valid == 1) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => '',
            "past" => '',
            "future" => ''
        );
        $dataContainer = array(
            "PK_mscdivision" => $divisionId -> value,
            "description" => $description -> value
        );
        if ($divisionId -> value == 'new-rec') {
            $modalLbl = array(
            "present" => 'Registration',
            "past" => 'Registered',
            "future" => 'Register'
            );
            $isSuccess = MscDivision::create($dataContainer);
        } else {
            $modalLbl = array(
            "present" => 'Updating',
            "past" => 'Updated',
            "future" => 'Update'
            );
            $isSuccess = MscDivision::update($dataContainer);
        }
        if ($isSuccess) {
            // die('waka');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Division ' . $modalLbl['present'] . ' Success</h2>
                    <p class="para-text">Division ' . $modalLbl['past'] . ' Successfully</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'btn-trigger',
                    "btnLbl" => 'OK',
                )
            );
        } else {
            // die('waka2');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize col-12">Error Encountered</h2>
                    <p class="para-text col-12">Error Details: Unable to ' . $modalLbl['future'] . ' Division Record</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        if ($divisionId -> valid == 0) {
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
        } else {
            $descriptionErr = new error_handler($description -> err_msg);

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
                                            <input type="text" class="form-control ' . $descriptionErr -> error_class . '" name="description" placeholder="Division Name" value="' . $description -> value . '">
                                            ' . $descriptionErr -> error_icon . '
                                            ' . $descriptionErr -> error_text . '
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
        }
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