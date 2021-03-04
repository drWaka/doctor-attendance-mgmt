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

if (isset($_POST['recordId'])) {
    $recordId = new form_validation($_POST['recordId'], 'str-int', 'Document ID', true);

    $voidedAttendanceRecord = '';
    if ($recordId -> valid == 1) {
        // Verify if the Employee ID is valid
        if ($recordId -> value == 'new-rec') {
            $voidedAttendanceRecord = array(
                "PK_employee_attendance_void" => 'TBD',
                "FK_employee" => '',
                "createDate" => date('Y-m-d')
            );
        } else {
            $voidedAttendanceRecord = EmployeeAttendanceVoid::show($recordId -> value);
            // die(var_dump($voidedAttendanceRecord));
            if (is_null($voidedAttendanceRecord)) {
                $recordId -> valid = 0;
                $recordId -> err_msg = "Record with Document ID not found";
            }
        }
    }

    if ($recordId -> valid == 1) {
        $employeeName = '';
        $employeeRecord = Employee::show($voidedAttendanceRecord['FK_employee']);
        if ($employeeRecord != null) {
            $employeeName = utf8_encode(strtoupper(("{$employeeRecord['lastName']}, {$employeeRecord['firstName']} {$employeeRecord['middleName']}")));
        }

        $response['content']['modal'] = modalize(
            '<div class="row">
                <div class="col-sm-12">
                <h2 class="header capitalize text-center">Log Validation Record Management</h2>
                <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                </div>
                
                <div class="col-sm-12 logs-validation-form">
                    <form form-name="respondent-form" action="../core/ajax/logs-validation-manage.php" tran-type="async-form">
                        <input type="text" name="recordId" hidden="hidden" value="' . $recordId -> value . '">

                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Document No. : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="documentNo" placeholder="Document No" value="' . $voidedAttendanceRecord['PK_employee_attendance_void'] . '" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Document Date : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="date" class="form-control" name="documentDate" value="' . date('Y-m-d', strtotime($voidedAttendanceRecord['createDate'])) . '" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Doctor Name : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="employeeId"  value="' . $voidedAttendanceRecord['FK_employee'] . '" hidden>
                                        <input type="text" class="form-control" name="employeeName" placeholder="Doctor Name" value="' . $employeeName . '" autocomplete="off">
                                        <div class="search-result">
                
                                            <div class="search-result-item row">
                                                <i class="">Start typing to search a doctor...</i>
                                            </div>
                                        </div>
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
                <p class='para-text col-12'>Error Details: {$recordId -> err_msg}</p>
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