<?php
// Special Cases for Reports
set_time_limit(1000); // Change Max Run Time Limit to 1000 secs

require '../php/_autoload.php';
require '../model/_autoload.php';

// die(var_dump($_POST));

if (isset($_POST['csvEmployeeName']) && isset($_POST['csvLogDate']) && isset($_POST['csvDepartmentId'])) {
    $csvEmployeeName = new form_validation($_POST['csvEmployeeName'], 'str', 'Employee Name', false);
    $csvLogDate = new form_validation($_POST['csvLogDate'], 'date', 'Log Date', true);
    $csvDepartmentId = new form_validation($_POST['csvDepartmentId'], 'str-int', 'Department ID', true);

    if ($csvEmployeeName -> valid == 1 && $csvLogDate -> valid == 1 && $csvDepartmentId -> valid == 1) {
        // Validate Department ID
        if ($csvDepartmentId -> value != 'all') {
            $department = MscDepartment::show($csvDepartmentId -> value);
            if (!(count($department) > 0)) {
                $csvDepartmentId -> valid = 0;
                $csvDepartmentId -> err_msg = 'Department ID is Invalid';
            }
        }
    }

    if ($csvEmployeeName -> valid == 1 && $csvLogDate -> valid == 1 && $csvDepartmentId -> valid == 1) {
        $csvContent = array();
        // CSV Header
        $csvContent = array(
            array('Our Lady of Lourdes Hospital'),
            array('Doctor Logs Summary Report - ' . date('F d, Y', strtotime($csvLogDate -> value))),
            array('Extraction Date: ' . date('F d, Y - h:i:s A')),
            array(''),
            array(
                'PRC No.',
                'Doctor Name',
                'Log Date',
                'Login', 
                'Logout',
                'Schedule'
            )
        );

        $where = '';
        if ($csvDepartmentId -> value != 'all' && is_numeric($csvDepartmentId -> value)) {
            $where .= "AND a.FK_mscDepartment = '{$csvDepartmentId -> value}'";
        }
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
                (CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%{$csvEmployeeName -> value}%'
                OR CONCAT(firstName, ' ', lastName) LIKE '%{$csvEmployeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), ' ', lastName) LIKE '%{$csvEmployeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), '. ', lastName) LIKE '%{$csvEmployeeName -> value}%'

                OR CONCAT(lastName, ', ', firstName, ' ', middleName) LIKE '%{$csvEmployeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName) LIKE '%{$csvEmployeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName, ' ', SUBSTR(middleName, 1, 1)) LIKE '%{$csvEmployeeName -> value}%'
                OR employeeNo LIKE '%{$csvEmployeeName -> value}%')

                AND b.attendance_date = '{$csvLogDate -> value}'
                AND b.isDelete != 1
                {$where}
            ORDER BY a.lastName, a.firstName , a.middleName 
        ";

        $employeeResult = $connection -> query($employeeQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_ASSOC);
        // die(var_dump($employeeRecords));
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
                $timeOut = (!is_null($employeeRecord['time_out'])) 
                    ? date('h:i:s A', strtotime($employeeRecord['time_out'])) 
                    : '-';
                $schedIn = date('h:i:s A', strtotime($employeeRecord['sched_start']));
                $schedOut = date('h:i:s A', strtotime($employeeRecord['sched_end']));

                $schedule = '-';
                if (strtoupper($schedIn) != '12:00:00 AM' && strtoupper($schedOut) != '12:00:00 AM') {
                    $schedule = "{$schedIn} - {$schedOut}";
                }

                // CSV Body
                $arrayIndex = count($csvContent);
                $csvContent[$arrayIndex] = array(
                    $employeeRecord['employeeNo'],
                    $employeeName,
                    date('Y-m-d', strtotime($employeeRecord['attendance_date'])),
                    $timeIn,
                    $timeOut,
                    $schedule
                );
            }
        }
        // die(var_dump($csvContent));

        $currentTimeStamp = strval(strtotime(date("Y-m-d h:i:s")));
        // die(date("Y-m-d H:i:s"));
        $filePath = "../files/csv/{$currentTimeStamp}.csv";
        $csvReport = fopen($filePath, 'w');

        foreach ($csvContent as $row) {
            fputcsv($csvReport, $row);
        }

        fclose($csvReport);
        //die(exec('whoami'));
        // File Name
        $fileName = 'Doctor Attendance Report.csv';
        
        // HTTP Headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header("Content-Disposition: attachment; filename=\"{$fileName}\";");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        ob_clean();
        flush();
        readfile($filePath);

    } else {
        $errorMessage = '';
        if ($csvEmployeeName -> valid == 0) {
            $errorMessage = $csvEmployeeName -> err_msg;
        } else if ($csvLogDate -> valid == 0) {
            $errorMessage = $csvLogDate -> err_msg;
        } else if ($csvDepartmentId -> valid == 0) {
            $errorMessage = $csvDepartmentId -> err_msg;
        }

        die('System Error Encountered: ' . $errorMessage);
    }

} else {
    die('System Error Encountered: Insufficient Data Submitted<br/> Please contact your System Administrator');
}