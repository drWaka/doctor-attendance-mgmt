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

if (isset($_POST['attendanceId'])) {
    $attendanceId = new form_validation($_POST['attendanceId'], 'int', 'Attendance ID', true);

    $department = '';
    if ($attendanceId -> valid == 1) {
        // Verify if the Employee ID is valid
        if ($departmentId -> value == 'new-rec') {
            $department = array(
                "description" => '',
                "specialization" => ''
            );
        } else {
            $department = MscDepartment::show($departmentId -> value);
            // die(var_dump($employee));
            if (is_null($department)) {
                $departmentId -> valid = 0;
                $departmentId -> err_msg = "Department Record not found";
            }
        }
    }

    if ($departmentId -> valid == 1) {
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
                                        <input type="text" class="form-control" name="description" placeholder="Department Name" value="' . $department['description'] . '">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Specialization: </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="specialization" placeholder="Department Specialization" value="' . $department['specialization'] . '">
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
                <p class='para-text col-12'>Error Details: {$departmentId -> err_msg}</p>
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