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

    // die(var_dump($timeIn));
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
        $attendanceFields = array(
            "FK_employee_delete" => $_SESSION['userId'],
            "PK_employee_attendance" => $attendanceId -> value
        );

        if (EmployeeAttendance::setInactive($attendanceFields)) {
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>Attendance Delete Success</h2>
                    <p class='para-text col-12'>Attendance Record has been successfully deleted.</p>
                </div>", 
                array(
                    "trasnType" => 'btn-trigger',
                    "btnLbl" => 'OK',
                )
            );
        } else {
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>Attendance Delete Failed</h2>
                    <p class='para-text col-12'>Error encountered during the attendance record delete <br> Please call your contact your system administrator.</p>
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