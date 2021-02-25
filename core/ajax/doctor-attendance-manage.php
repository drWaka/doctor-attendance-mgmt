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

if (isset($_POST['attendanceId']) && isset($_POST['timeIn']) && isset($_POST['timeOut'])) {
    $attendanceId = new form_validation($_POST['attendanceId'], 'int', 'Attendance ID', true);
    $timeIn = new form_validation($_POST['timeIn'], 'time', 'Time In', true);
    $timeOut = new form_validation($_POST['timeOut'], 'time', 'Time Out', false);

    // die(var_dump($timeIn));
    $attendance = '';
    if ($attendanceId -> valid == 1 && $timeIn -> valid == 1 && $timeOut -> valid == 1) {
        // Verify if the Attendance ID is valid
        $attendance = EmployeeAttendance::show($attendanceId -> value);
        // die(var_dump($attendance));
        if (is_null($attendance)) {
            $attendanceId -> valid = 0;
            $attendanceId -> err_msg = "Attendance Record not found";
        }
    }

    if ($attendanceId -> valid == 1 && $timeIn -> valid == 1 && $timeOut -> valid == 1) {
        $timeInVal = "{$attendance['attendance_date']} {$timeIn -> value}";
        $timeOutVal = !empty($timeOut -> value)
            ? "{$attendance['attendance_date']} {$timeOut -> value}"
            : '';

        $attendanceFields = array(
            "time_in" => $timeInVal,
            "time_out" => $timeOutVal,
            "FK_employee_update" => $_SESSION['userId'],
            "PK_employee_attendance" => $attendanceId -> value
        );

        if (EmployeeAttendance::update($attendanceFields)) {
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>Attendance Update Success</h2>
                    <p class='para-text col-12'>Attendance Record has been successfully updated.</p>
                </div>", 
                array(
                    "trasnType" => 'btn-trigger',
                    "btnLbl" => 'OK',
                )
            );
        } else {
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>Attendance Update Failed</h2>
                    <p class='para-text col-12'>Error encountered during the attendance record update <br> Please call your contact your system administrator.</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }        
    } else {
        if ($attendanceId -> valid == 0) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$attendanceId -> err_msg}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        } else {
            $timeInErr = new error_handler($timeIn -> err_msg);
            $timeOutErr = new error_handler($timeOut -> err_msg);

            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Attendance Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="division-form" action="../core/ajax/doctor-attendance-manage.php" tran-type="async-form">
                            <input type="text" name="attendanceId" hidden="hidden" value="' . $attendanceId -> value . '">
    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Login Time: </label>
                                        <div class="form-group col-sm-12">
                                            <input type="time" class="form-control ' . $timeInErr -> error_class . '" name="timeIn" value="' . $timeIn -> value . '">
                                            ' . $timeInErr -> error_icon . '
                                            ' . $timeInErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Logout Time: </label>
                                        <div class="form-group col-sm-12">
                                            <input type="time" class="form-control ' . $timeOutErr -> error_class . '" name="timeOut" value="' . $timeOut -> value . '">
                                            ' . $timeOutErr -> error_icon . '
                                            ' . $timeOutErr -> error_text . '
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