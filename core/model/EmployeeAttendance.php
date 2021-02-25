<?php

class EmployeeAttendance {

    public static function index() {
        $query = "SELECT * employee_attendance employee_clinic_sched";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function insert($data) {
        $timeout = (!empty($data['time_out'])) ? "'{$data['time_out']}'" : 'NULL';
        $query = "
            INSERT INTO employee_attendance(
                FK_employee, attendance_date, time_in, 
                time_out, sched_start, sched_end, 
                FK_employee_update
            ) VALUES (
                '{$data['FK_employee']}', '{$data['attendance_date']}', '{$data['time_in']}', 
                {$timeout}, '{$data['sched_start']}', '{$data['sched_end']}', 
                '{$data['FK_employee_update']}'
            )
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function show($id) {
        $query = "SELECT * FROM employee_attendance WHERE PK_employee_attendance = '{$id}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return null;
    }
    public static function update($data) {
        $timeOutVal = 'NULL';
        if (!empty($data['time_out'])) {
            $timeOutVal = "'{$data['time_out']}'";
        }
        $query = "
            UPDATE employee_attendance
            SET time_in = '{$data['time_in']}',
                time_out = {$timeOutVal},
                FK_employee_update = '{$data['FK_employee_update']}'
            WHERE PK_employee_attendance = '{$data['PK_employee_attendance']}'
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function setInactive ($data) {
        $currentDate = date('Y-m-d H:i:s');
        $query = "
            UPDATE employee_attendance
            SET isDelete = 1,
                FK_employee_delete = '{$data['FK_employee_delete']}',
                deleteDate = '{$currentDate}'
            WHERE PK_employee_attendance = '{$data['PK_employee_attendance']}'
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function delete ($id) {
        return false;
    }

    public static function getByEmployeeId($employeeNo) {
        return self::filter(array(
            "employeeId" => $employeeNo
        ));
    }

    public static function filter($filter) {
        $where = "";
        if (isset($filter['employeeId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " FK_employee = '{$filter['employeeId']}' ";
        }

        if (isset($filter['day'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " sched_day = '{$filter['day']}' ";
        }

        $query = "SELECT * FROM employee_attendance {$where}";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
}