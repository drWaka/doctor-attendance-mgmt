<?php

class EmployeeClinicSchedule {

    public static function index() {
        $query = "
            SELECT * FROM employee_clinic_sched 
            ORDER BY 
            CASE sched_day
                WHEN 'SUN' THEN 1
                WHEN 'MON' THEN 2
                WHEN 'TUE' THEN 3
                WHEN 'WED' THEN 4
                WHEN 'THU' THEN 5
                WHEN 'FRI' THEN 6
                WHEN 'SAT' THEN 7
            END
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function insert($data) {
        return false;
    }

    public static function show($id) {
        $query = "SELECT * FROM employee_clinic_sched WHERE PK_employee_clinic_sched = '{$id}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return null;
    }
    public static function update($data) {
        $query = "
            UPDATE employee_clinic_sched
            SET time_start = '{$data['time_start']}',
                time_end = '{$data['time_end']}'
            WHERE PK_employee_clinic_sched = '{$data['PK_employee_clinic_sched']}'
        ";
        // die($query);
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
            "employeId" => $employeeNo
        ));
    }

    public static function filter($filter) {
        $where = "";
        if (isset($filter['employeId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " FK_employee = '{$filter['employeId']}' ";
        }

        if (isset($filter['day'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " sched_day = '{$filter['day']}' ";
        }

        $query = "SELECT * FROM employee_clinic_sched {$where}
            ORDER BY 
            CASE sched_day
                WHEN 'SUN' THEN 1
                WHEN 'MON' THEN 2
                WHEN 'TUE' THEN 3
                WHEN 'WED' THEN 4
                WHEN 'THU' THEN 5
                WHEN 'FRI' THEN 6
                WHEN 'SAT' THEN 7
            END
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
}