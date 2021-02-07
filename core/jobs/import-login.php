<?php
require '../php/_autoload.php';
require '../model/_autoload.php';

$currentDate = date('Y-m-d');
// Select all Active Doctors that are not yet logged
$activeDoctorsQry = "
    SELECT a.PK_employee, a.employeeNo
    FROM employees AS a 
    WHERE a.isDeleted = 0
        AND a.PK_employee NOT IN (
            SELECT x.FK_employee FROM employee_attendance AS x 
            WHERE x.attendance_date = '{$currentDate}'
        )
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
            WHERE a.FunctionKey IN (1, 0)
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
                $doctorSchedule = EmployeeClinicSchedule::filter(array(
                    "employeeId" => $activeDoctorsRow['PK_employee'],
                    "day" => date('D')
                ));
                if (!(EmployeeAttendance::insert(array(
                    "FK_employee" => $activeDoctorsRow['PK_employee'],
                    "attendance_date" => $currentDate,
                    "time_in" => date('Y-m-d H:i:s', strtotime($attendanceRow[0]['TransactionTime'])),
                    "time_out" => '',
                    "sched_start" => $currentDate . " " . date('H:i:s', strtotime($doctorSchedule[0]['time_start'])),
                    "sched_end" => $currentDate . " " . date('H:i:s', strtotime($doctorSchedule[0]['time_end'])),
                    "FK_employee_update" => 0
                )))) {
                    die('Unable to Import Doctor Attendance');
                }
            }
        }
    }
}