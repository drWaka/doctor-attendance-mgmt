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
    isset($_POST['unitId']) && 
    isset($_POST['description']) && 
    isset($_POST['departmentId'])
) {
    $unitId = new form_validation($_POST['unitId'], 'str-int', 'Unit ID', true);
    $description = new form_validation($_POST['description'], 'str-int', 'Unit Name', true);
    $departmentId = new form_validation($_POST['departmentId'], 'int', 'Department ID', true);
    
    $flags = array(
        'hasError' => 0,
        'isDone' => 0
    );
    if ($unitId -> valid == 1 && $description -> valid == 1 && $departmentId -> valid == 1) {
        // Verify if the Employee ID is valid
        $unit = MscUnit::show($unitId -> value);
        if (is_null($unit)) {
            if ($unitId -> value !== 'new-rec') {
                $unitId -> valid = 0;
                $unitId -> err_msg = "Unit Record not found";
            }
        } else {
            $isExists = MscUnit::checkUnique(array(
                "unitName" => $description -> value,
                "unitId" => $unitId -> value,
                "departmentId" => $departmentId -> value
            ));

            if (count($isExists)) {
                $departmentId -> valid = 0;
                $departmentId -> err_msg = "Unit Record already exists";
            }
        }
    }

    if ($unitId -> valid == 1 && $description -> valid == 1 && $departmentId -> valid == 1) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => '',
            "past" => '',
            "future" => ''
        );
        $dataContainer = array(
            "departmentId" => $departmentId -> value,
            "description" => $description -> value,
            "unitId" => $unitId -> value
        );
        if ($unitId -> value == 'new-rec') {
            $modalLbl = array(
            "present" => 'Registration',
            "past" => 'Registered',
            "future" => 'Register'
            );
            $isSuccess = MscUnit::create($dataContainer);
        } else {
            $modalLbl = array(
            "present" => 'Updating',
            "past" => 'Updated',
            "future" => 'Update'
            );
            $isSuccess = MscUnit::update($dataContainer);
        }
        if ($isSuccess) {
            // die('waka');
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
            // die('waka2');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize col-12">Error Encountered</h2>
                    <p class="para-text col-12">Error Details: Unable to ' . $modalLbl['future'] . ' Unit Record</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        if ($unitId -> valid == 0) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$unitId -> err_msg}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        } else {
            $descriptionErr = new error_handler($description -> err_msg);
            $departmentIdErr = new error_handler($departmentId -> err_msg);

            $departmentElem = "";
            $department = MscDepartment::index();
            $departmentElem .= "
                <select name='departmentId' id='' class='form-control {$departmentIdErr -> error_class}'>
                    <option value='' style='display:none;'>Select Department</option>
            ";
            if (is_array($department)) {
                if (count($department) > 0) {
                    foreach($department as $row) {
                        $selected = ($row['PK_mscdepartment'] == $departmentId -> value) 
                            ? 'selected' 
                            : '';
                        $departmentElem .= "<option value='{$row['PK_mscdepartment']}' {$selected}>{$row['description']}</option>";
                    }
                }
            }
            $departmentElem .= "</select>";
            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Unit Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="division-form" action="../core/ajax/unit-manage.php" tran-type="async-form">
                            <input type="text" name="unitId" hidden="hidden" value="' . $unitId -> value . '">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Unit Name: </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $descriptionErr -> error_class . '" name="description" placeholder="Unit Name" value="' . $description -> value . '">
                                            ' . $descriptionErr -> error_icon . '
                                            ' . $descriptionErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Department: </label>
                                        <div class="form-group col-sm-12">
                                            ' . $departmentElem . '
                                            ' . $departmentIdErr -> error_icon . '
                                            ' . $departmentIdErr -> error_text . '
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