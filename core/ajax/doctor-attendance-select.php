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

    $attendance = '';
    if ($attendanceId -> valid == 1) {
        // Verify if the Attendance ID is valid
        $attendance = EmployeeAttendance::show($attendanceId -> value);
        // die(var_dump($attendance));
        if (is_null($attendance)) {
            $attendanceId -> valid = 0;
            $attendanceId -> err_msg = "Attendance Record not found";
        }
    }

    if ($attendanceId -> valid == 1) {
        $timeIn = (!empty($attendance['time_in'])) ? 
            date('H:i:s', strtotime($attendance['time_in'])) 
            : '';
        $timeOut = (!empty($attendance['time_out'])) ? 
            date('H:i:s', strtotime($attendance['time_out'])) 
            : '';
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
                                        <input type="time" class="form-control" name="timeIn" value="' . $timeIn . '">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Logout Time: </label>
                                    <div class="form-group col-sm-12">
                                        <input type="time" class="form-control" name="timeOut" value="' . $timeOut . '">
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
                <p class='para-text col-12'>Error Details: {$attendanceId -> err_msg}</p>
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