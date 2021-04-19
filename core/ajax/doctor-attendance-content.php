<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => '',
        "record" => '',
        "total" => 0
    ),
    "contentType" => ''
);

if (
    isset($_POST['employeeName']) && 
    isset($_POST['logDate']) &&
    isset($_POST['departmentId']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Doctor Name', false);
    $departmentId = new form_validation($_POST['departmentId'], 'str-int', 'Department', true);
    $logDate = new form_validation($_POST['logDate'], 'str-int', 'Log Date', true);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if (
        $employeeName -> valid == 1 && $departmentId -> valid == 1 && $logDate -> valid == 1 && 
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
    ) {
      // Validate Department ID
     if ($departmentId -> value != 'all') {
        $department = MscDepartment::show($departmentId -> value);
        if (!(count($department) > 0)) {
          $departmentId -> valid = 0;
          $departmentId -> err_msg = 'Department ID is Invalid';
        }
     }
      
    }

    if (
        $employeeName -> valid == 1 && $departmentId -> valid == 1 && $logDate -> valid == 1 && 
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
     ) {
        $employeeQuery = "
            SELECT 
                b.PK_employee_attendance
                , a.employeeNo
                , a.lastName
                , a.firstName
                , a.middleName
                , b.attendance_date
                , b.time_in
                , b.time_out
                , b.sched_start
                , b.sched_end
            FROM employees AS a
            LEFT OUTER JOIN employee_attendance AS b ON a.PK_employee = b.FK_employee
            WHERE 
                (CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), '. ', lastName) LIKE '%{$employeeName -> value}%'

                OR CONCAT(lastName, ', ', firstName, ' ', middleName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName, ' ', SUBSTR(middleName, 1, 1)) LIKE '%{$employeeName -> value}%'
                OR employeeNo LIKE '%{$employeeName -> value}%')

                AND b.attendance_date = '{$logDate -> value}'
                AND b.isDelete != 1
                
        ";

        if ($departmentId -> value != 'all' && is_numeric($departmentId -> value)) {
            $employeeQuery .= "AND a.FK_mscDepartment = '{$departmentId -> value}'";
        }

        // die($employeeQuery);
        $employeeResult = $connection -> query($employeeQuery);
        $response['content']['total'] = $employeeResult -> num_rows;
        
        $offset = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
        $employeeQuery .= "
            ORDER BY a.lastName, a.firstName, a.middleName
            LIMIT {$pageLimit -> value} OFFSET {$offset}
        ";
        $employeeResult = $connection -> query($employeeQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_ASSOC);

        $response['content']['record'] = '';
        if (count($employeeRecords) > 0) {
            foreach ($employeeRecords as $employeeRecord) {
                // Employee Name Field
                $middleInitial = !empty($employeeRecord['middleName'])
                    ? substr($employeeRecord['middleName'], 0, 1) . '.'
                    : '';
                $employeeName = utf8_encode(strtoupper(("{$employeeRecord['lastName']}, {$employeeRecord['firstName']} {$middleInitial}")));

                // die(var_dump($employeeRecord['time_out']) . 'WAKA');
                $attendanceDate = date('F d, Y', strtotime($employeeRecord['attendance_date']));
                $timeIn = date('h:i:s A', strtotime($employeeRecord['time_in']));
                $timeOut = (!empty($employeeRecord['time_out'])) ? date('h:i:s A', strtotime($employeeRecord['time_out'])) : '-';
                $schedIn = date('h:i:s A', strtotime($employeeRecord['sched_start']));
                $schedOut = date('h:i:s A', strtotime($employeeRecord['sched_end']));

                $schedule = '&minus;';
                if (strtoupper($schedIn) != '12:00:00 AM' && strtoupper($schedOut) != '12:00:00 AM') {
                    $schedule = "{$schedIn} &minus; {$schedOut}";
                }

                // Data Management Field
                $dataManagementBtn = '';
                if ($_SESSION['userType'] == 'administrator') {
                    $dataManagementBtn = "
                        <td class='text-center'>
                            <button class='btn btn-success transaction-btn' title='Edit Respondent'
                                trans-name='async-form'
                                data-target='.modal-container'
                                data-link='../core/ajax/doctor-attendance-select.php'
                                data-content='{
                                    &quot;attendanceId&quot; : &quot;{$employeeRecord['PK_employee_attendance']}&quot;
                                }'
                            ><i class='fas fa-pencil-alt'></i></button>
                            <button class='btn btn-danger transaction-btn' title='Delete Department'
                                trans-name='async-form'
                                data-target='.modal-container'
                                data-link='../core/ajax/generic-warning-modal.php'
                                data-content='{
                                    &quot;transType&quot;   : &quot;delete&quot;,
                                    &quot;link&quot;        : &quot;../core/ajax/doctor-attendance-delete.php&quot;,
                                    &quot;dataContent&quot; : {
                                        &quot;attendanceId&quot;  : &quot;{$employeeRecord['PK_employee_attendance']}&quot;
                                    },
                                    &quot;headerTitle&quot; : &quot;Doctor Log&quot;
                                }'
                            ><i class='fa fa-trash'></i></button>
                        </td>
                    ";
                }
                

                $response['content']['record'] .= "<tr>";
                $response['content']['record'] .= "
                    <td>{$employeeRecord['employeeNo']}</td>
                    <td>{$employeeName}</td>
                    <td>{$attendanceDate}</td>
                    <td>{$timeIn}</td>
                    <td>{$timeOut}</td>
                    <td>{$schedule}</td>
                    {$dataManagementBtn}
                ";
                $response['content']['record'] .= "<tr>";
            }
        } else {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] = '<tr><td class="text-center" colspan="7">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($employeeName -> valid == 0) {
            $errorMessage = $employeeName -> err_msg;
        } else if ($departmentId -> valid == 0) {
            $errorMessage = $departmentId -> err_msg;
        } else if ($logDate -> valid == 0) {
            $errorMessage = $logDate -> err_msg;
        } else if ($pageLimit -> valid == 0) {
            $errorMessage = $pageLimit -> err_msg;
        } else if ($currentPage -> valid == 0) {
            $errorMessage = $currentPage -> err_msg;
        }

        $response['success'] = 'failed';
        $response['contentType'] = 'modal';
        $response['content']['modal'] = modalize(
            "<div class='row text-center'>
                <h2 class='header capitalize col-12'>System Error Encountered</h2>
                <p class='para-text col-12'>Error Details: {$errorMessage}</p>
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