<?php

class EmployeeAttendanceVoidContent {

    public static function index() {
        $query = "SELECT * FROM employee_attendance_void_content";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function insert($data) {
        $query = "
            INSERT INTO employee_attendance_void_content (
                FK_employee_attendance_void, FK_biometric_log_id
            ) VALUES (
               '{$data['logValidationId']}', '{$data['bioTransactId']}'
            )
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function show($id) {
        $query = "SELECT * FROM employee_attendance_void_content WHERE PK_employee_attendance_void_content = '{$id}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return null;
    }
    public static function update($data) {
        $data = self::escape_string($data);
        $query = "";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function delete ($id) {
        $query = "DELETE FROM employee_attendance_void_content WHERE PK_employee_attendance_void_content = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function filter($filter) {
        // Filter
        $where = "";
        if (isset($filter['recordId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.PK_employee_attendance_void_content = '{$filter['recordId']}' ";
        }

        if (isset($filter['logValidationId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.FK_employee_attendance_void = '{$filter['logValidationId']}' ";
        }

        // Pagination
        $pagination = "";
        if (isset($filter['page']) && isset($filter['limit'])) {
            $offset = ((intval($filter['page']) - 1 ) * $filter['limit']);
            $pagination = "LIMIT {$filter['limit']} OFFSET {$offset}";
        }

        $query = "
            SELECT
                PK_employee_attendance_void_content, FK_employee_attendance_void, FK_biometric_log_id
            FROM employee_attendance_void_content AS a
            {$where}
            {$pagination}
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function escape_string($data) {
        if (is_array($data)) {
            foreach($data as $key => $value) {
                $data[$key] = $GLOBALS['connection'] -> real_escape_string($value);
            }
            return $data;
        }

        return $GLOBALS['connection'] -> real_escape_string($data);
    }

}