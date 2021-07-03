<?php
require '../php/_autoload.php';
require '../model/_autoload.php';

$currentDate = date('Y-m-d', strtotime('2021-05-01'));
$limitDate = date('Y-m-d');

while(strtotime($currentDate) <= strtotime($limitDate)) {
    // Select all Active Doctors that are not yet logged
    $activeDoctorsQry = "
    SELECT a.PK_employee, a.employeeNo, a.fingerScanId
    FROM employees AS a 
    WHERE a.isDeleted = 0
        AND a.fingerScanId IS NOT NULL
        AND a.PK_employee NOT IN (
            SELECT x.FK_employee FROM employee_attendance AS x 
            WHERE x.attendance_date = '{$currentDate}'
                AND x.isDelete != 1
        )
    ";
    $activeDoctorsRes = $connection -> query($activeDoctorsQry);
    $activeDoctorsRows = $activeDoctorsRes -> fetch_all(MYSQLI_ASSOC);
    if (count($activeDoctorsRows) > 0) {
        foreach ($activeDoctorsRows as $activeDoctorsRow) {
            // Select all of the Employee's excluded Logs
            $excludedLogsQry = "
                SELECT b.FK_biometric_log_id 
                FROM employee_attendance_void AS a
                INNER JOIN employee_attendance_void_content AS b 
                    ON a.PK_employee_attendance_void = b.FK_employee_attendance_void
                WHERE date_format(a.createDate, \"%Y-%m-%d\") = '{$currentDate}'
                    AND a.FK_employee = '{$activeDoctorsRow['PK_employee']}'
                    AND a.isPosted = 1
                    AND (a.isCancelled != 1 OR a.isCancelled IS NULL)
                    AND (a.isVoided != 1 OR a.isVoided IS NULL)
            ";
            $excludedLogsRes = $connection -> query($excludedLogsQry);

            $excludedBioLogId = '';
            if ($excludedLogsRes -> num_rows > 0) {
                while ($excludedLogsRow = $excludedLogsRes -> fetch_assoc()) {
                    $excludedBioLogId .= (strlen($excludedBioLogId) > 0) ? "','" : '';
                    $excludedBioLogId .= $excludedLogsRow['FK_biometric_log_id'];

                }
                $excludedBioLogId = "AND a.IndexKey NOT IN ('{$excludedBioLogId}')";
            }

            // Capture Data from Biomectrics Database
            $attendanceQry = "
                SELECT TOP 1 a.TransactionTime
                FROM NGAC_AUTHLOG AS a 
                INNER JOIN NGAC_USERINFO AS b ON a.UserIDIndex = b.IndexKey
                WHERE a.FunctionKey IN (1, 0)
                    AND a.AuthResult = 0
                    AND b.ID = '{$activeDoctorsRow['fingerScanId']}'
                    AND CONVERT(DATE, a.TransactionTime) = '{$currentDate}'
                    {$excludedBioLogId}
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

    $currentDate = date('Y-m-d', strtotime($currentDate . ' + 1 days'));
}