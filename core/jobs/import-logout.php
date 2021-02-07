<?php
phpinfo();
die();
require '../php/_autoload.php';
require '../model/_autoload.php';

$currentDate = date('Y-m-d');
// $currentDate = '2021-02-06';
// Select all Active Doctors that are not yet logged
$activeDoctorsQry = "
    SELECT a.PK_employee, a.employeeNo, b.PK_employee_attendance, b.time_in, b.time_out
    FROM employees AS a
    INNER JOIN employee_attendance AS b ON a.PK_employee = b.FK_employee
    WHERE a.isDeleted = 0
        AND b.attendance_date = '{$currentDate}' 
        AND b.time_out IS NULL
";
$activeDoctorsRes = $connection -> query($activeDoctorsQry);
$activeDoctorsRows = $activeDoctorsRes -> fetch_all(MYSQLI_ASSOC);
if (count($activeDoctorsRows) > 0) {
    foreach ($activeDoctorsRows as $activeDoctorsRow) {
        // Capture Data from Biomectrics Database
        $attendanceQry = "
            SELECT TOP 1 a.TransactionTime
            FROM NGAC_AUTHLOG AS a 
            INNER JOIN NGAC_USERINFO AS b ON a.UserIDIndex = b.IndexKey
            WHERE a.FunctionKey IN (2)
                AND a.AuthResult = 0
                AND b.ID = '{$activeDoctorsRow['employeeNo']}'
                AND CONVERT(DATE, a.TransactionTime) = '{$currentDate}'
            ORDER BY a.TransactionTime
        ";
        $attendanceRes = $bioConnection -> prepare($attendanceQry);
        $attendanceRes -> execute();
        $attendanceRow = $attendanceRes -> fetchAll();

        if (is_array($attendanceRow)) {
            if (count($attendanceRow) > 0) {
                if (!(EmployeeAttendance::update(array(
                    "time_in" => $activeDoctorsRow['time_in'],
                    "time_out" => date('Y-m-d H:i:s', strtotime($attendanceRow[0]['TransactionTime'])),
                    "PK_employee_attendance" => $activeDoctorsRow['PK_employee_attendance'],
                    "FK_employee_update" => 0
                )))) {
                    die('Unable to Import Doctor Attendance');
                }
            }
        }
    }
}