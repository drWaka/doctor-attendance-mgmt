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

if (isset($_POST['employeeId'])) {
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Doctor ID', true);

    if ($employeeId -> valid == 1) {
        // Verify if the Doctor ID is Valid
        $doctor = Employee::show($employeeId -> value);
        if (is_null($doctor)) {
            $employeeId -> valid = 0;
            $employeeId -> err_msg = "Doctor Record not found";
        }
    }

    if ($employeeId -> valid == 1) {
        $modalContent = '';

        $employeeSched = EmployeeClinicSchedule::getByEmployeeId($employeeId -> value);
        foreach ($employeeSched as $sched) {
            $days = [
                'SUN' => 'Sunday',
                'MON' => 'Monday',
                'TUE' => 'Tuesday',
                'WED' => 'Wednesday',
                'THU' => 'Thursday',
                'FRI' => 'Friday',
                'SAT' => 'Saturday'
            ];
            $timeStart = date('H:i:s', strtotime($sched['time_start']));
            $timeEnd = date('H:i:s', strtotime($sched['time_end']));
            $modalContent .= "
                <div class='col-md-12'>
                <label for=' class='text-left control-label col-sm-12'>{$days[$sched['sched_day']]} Schedule: </label>
                    <div class='row'>
                        
                        <div class='form-group col-sm-6'>
                            <input type='time' class='form-control' name='" . strtolower($sched['sched_day']) . "SchedStart' value='{$timeStart}'>
                        </div>
                        <div class='form-group col-sm-6'>
                            <input type='time' class='form-control' name='" . strtolower($sched['sched_day']) . "SchedEnd' value='{$timeEnd}'>
                        </div>
                    </div>
                </div>
            ";
        }

        $response['content']['modal'] = modalize(
            '<div class="row">
                <div class="col-sm-12">
                <h2 class="header capitalize text-center">Doctor Schedule Management</h2>
                <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                </div>
                
                <div class="col-sm-12 item-guide-mgmt">
                    <form form-name="doctor-sched-form" action="../core/ajax/doctor-schedule-manage.php" tran-type="async-form">
                        <input type="text" name="employeeId" hidden="hidden" value="' . $employeeId -> value . '">

                        <div class="row">' . $modalContent . '</div>

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
                <p class='para-text col-12'>Error Details: {$employeeId -> err_msg}</p>
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