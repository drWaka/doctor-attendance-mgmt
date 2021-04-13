<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "httpStatus" => 'success',
    "type" => '',
    "level" => '',
    "content" => ''
);

if (isset($_POST['currentPage']) && isset($_POST['itemLimit'])) {
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Current Page', true);
    $itemLimit = new form_validation($_POST['itemLimit'], 'int', 'Page Limit', true);

    if ($currentPage -> valid == 1 && $itemLimit -> valid == 1) {
        $attendance = EmployeeAttendance::filter(array(
            "isOnBoard" => 1,
            "date" => date('Y-m-d')
        ));

        $attendancePaginated = array();
        $startNode = (($currentPage -> value - 1) * $itemLimit -> value); 
        $limitNode = $startNode + $itemLimit -> value;
        for ($i = $startNode ; $i < $limitNode ; $i++) {
            if (isset($attendance[$i])) {
                $currentIndex = count($attendancePaginated);
                $attendancePaginated[$currentIndex] = array();
                $attendancePaginated[$currentIndex][0] = $attendance[$i]['clinic'];
                $attendancePaginated[$currentIndex][1] = strtoupper($attendance[$i]['name']);
                $attendancePaginated[$currentIndex][2] = strtoupper($attendance[$i]['department']);

                $schedule = EmployeeClinicSchedule::filter(array(
                    "employeeId" => $attendance[$i]['PK_employee'],
                    "day" => date('D')
                ));
                $timeIn = date('h:i A', strtotime($schedule[0]['time_start']));
                $timeOut = date('h:i A', strtotime($schedule[0]['time_end']));
                if ($timeIn != '12:00 AM' && $timeOut != '12:00 AM') {
                    $attendancePaginated[$currentIndex][3] = "{$timeIn} - {$timeOut}";
                } else {
                    $attendancePaginated[$currentIndex][3] = "-";
                }
            }
        }

        $response['content'] = array(
            "totalPages" => ceil(count($attendance) / $itemLimit -> value),
            "record" => $attendancePaginated
        );
    } else {
        $errorMessage = '';
        if ($currentPage -> valid == 0) {
            $errorMessage = $currentPage -> err_msg;
        } else if ($itemLimit -> valid == 0) {
            $errorMessage = $itemLimit -> err_msg;
        }

        $response['httpStatus'] = 'failed';
        $response['type'] = 'notif';
        $response['level'] = 'warning';
        $response['content'] = $errorMessage;
    }
} else {
    $response['httpStatus'] = 'failed';
    $response['type'] = 'notif';
    $response['level'] = 'warning';
    $response['content'] = 'Insufficient Data Submitted. Please refresh the page and try again.<br/> If the issue persists please contact your System Administrator';
}


// Encode JSON Response
encode_json_file($response);
