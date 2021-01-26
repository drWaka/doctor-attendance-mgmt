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
    isset($_POST['departmentId']) && 
    isset($_POST['description']) && 
    isset($_POST['specialization']) 
) {
    $departmentId = new form_validation($_POST['departmentId'], 'str-int', 'Department ID', true);
    $description = new form_validation($_POST['description'], 'str-int', 'Department Name', true);
    $specialization = new form_validation($_POST['specialization'], 'str-int', 'Department Specalization', true);
    
    $flags = array(
        'hasError' => 0,
        'isDone' => 0
    );
    if ($departmentId -> valid == 1 && $description -> valid == 1 && $specialization -> valid == 1) {
        // Verify if the Employee ID is valid
        $employee = MscDepartment::show($departmentId -> value);
        if (is_null($employee)) {
            if ($departmentId -> value !== 'new-rec') {
                $departmentId -> valid = 0;
                $departmentId -> err_msg = "Department Record not found";
            }
        } else {
            $isExists = MscDepartment::checkUnique(array(
                "description" => $description -> value,
                "departmentId" => $departmentId -> value
            ));

            if (count($isExists)) {
                $departmentId -> valid = 0;
                $departmentId -> err_msg = "Department Record already exists";
            }
        }
    }

    if ($departmentId -> valid == 1 && $description -> valid == 1 && $specialization -> valid == 1) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => '',
            "past" => '',
            "future" => ''
        );
        $dataContainer = array(
            "departmentId" => $departmentId -> value,
            "description" => $description -> value,
            "specialization" => $specialization -> value,
        );
        if ($departmentId -> value == 'new-rec') {
            $modalLbl = array(
            "present" => 'Registration',
            "past" => 'Registered',
            "future" => 'Register'
            );
            $isSuccess = MscDepartment::create($dataContainer);
        } else {
            $modalLbl = array(
            "present" => 'Updating',
            "past" => 'Updated',
            "future" => 'Update'
            );
            $isSuccess = MscDepartment::update($dataContainer);
        }
        if ($isSuccess) {
            // die('waka');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Department ' . $modalLbl['present'] . ' Success</h2>
                    <p class="para-text">Department ' . $modalLbl['past'] . ' Successfully</p>
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
                    <p class="para-text col-12">Error Details: Unable to ' . $modalLbl['future'] . ' Department Record</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        if ($departmentId -> valid == 0) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$departmentId -> err_msg}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        } else {
            $descriptionErr = new error_handler($description -> err_msg);
            $specializationErr = new error_handler($specialization -> err_msg);

            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Department Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="division-form" action="../core/ajax/department-manage.php" tran-type="async-form">
                            <input type="text" name="departmentId" hidden="hidden" value="' . $departmentId -> value . '">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Department Name: </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $descriptionErr -> error_class . '" name="description" placeholder="Department Name" value="' . $description -> value . '">
                                            ' . $descriptionErr -> error_icon . '
                                            ' . $descriptionErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Specialization: </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control  ' . $specializationErr -> error_class . '" name="specialization" placeholder="Department Specialization" value="' . $specialization -> value . '">
                                            ' . $specializationErr -> error_icon . '
                                            ' . $specializationErr -> error_text . '
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