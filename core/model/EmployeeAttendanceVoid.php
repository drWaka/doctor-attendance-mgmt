<?php

class EmployeeAttendanceVoid {

    public static function index() {
        $query = "SELECT * FROM employee_attendance_void";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function insert($data) {
        $query = "
            INSERT INTO employee_attendance_void (
                FK_employee, FK_user_create, createDate
            ) VALUES (
                '{$data['FK_employee']}', '{$data['FK_user_create']}', '{$data['createDate']}'
            )
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function show($id) {
        $query = "SELECT * FROM employee_attendance_void WHERE PK_employee_attendance_void = '{$id}'";
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

    public static function post($data) {
        $query = "
            UPDATE employee_attendance_void
            SET isPosted = 1,
                postDate = '{$data['transDate']}',
                FK_user_post = '{$data['FK_user']}'
            WHERE PK_employee_attendance_void = '{$data['recordId']}'
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) return true;
        
        return false;
    }

    public static function cancel($data) {
        $query = "
            UPDATE employee_attendance_void
            SET isCancelled = 1,
                cancelDate = '{$data['transDate']}',
                FK_user_cancel = '{$data['FK_user']}'
            WHERE PK_employee_attendance_void = '{$data['recordId']}'
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) return true;
        
        return false;
    }

    public static function void($data) {
        $query = "
            UPDATE employee_attendance_void
            SET isVoided = 1,
                voidDate = '{$data['transDate']}',
                FK_user_void = '{$data['FK_user']}'
            WHERE PK_employee_attendance_void = '{$data['recordId']}'
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) return true;
        
        return false;
    }

    public static function delete ($id) {
        $query = "DELETE FROM employee_attendance_void WHERE PK_employee_attendance_void = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function filter($filter) {
        // Filter
        $where = "";
        if (isset($filter['employeeName_Id'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " 
                (CONCAT(b.firstName, ' ', b.middleName, ' ', b.lastName) LIKE '%{$filter['employeeName_Id']}%'
                OR CONCAT(b.firstName, ' ', b.lastName) LIKE '%{$filter['employeeName_Id']}%'
                OR CONCAT(b.firstName, ' ', SUBSTR(b.middleName, 1, 1), ' ', b.lastName) LIKE '%{$filter['employeeName_Id']}%'
                OR CONCAT(b.firstName, ' ', SUBSTR(b.middleName, 1, 1), '. ', b.lastName) LIKE '%{$filter['employeeName_Id']}%'

                OR CONCAT(b.lastName, ', ', b.firstName, ' ', b.middleName) LIKE '%{$filter['employeeName_Id']}%'
                OR CONCAT(b.lastName, ', ', b.firstName) LIKE '%{$filter['employeeName_Id']}%'
                OR CONCAT(b.lastName, ', ', b.firstName, ' ', SUBSTR(b.middleName, 1, 1)) LIKE '%{$filter['employeeName_Id']}%'
                OR b.employeeNo LIKE '%{$filter['employeeName_Id']}%') 
            ";
        }

        if (isset($filter['FK_employee'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.FK_employee = '{$filter['FK_employee']}' ";
        }

        if (isset($filter['documentDate'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " DATE_FORMAT(a.createDate, \"%Y-%m-%d\") = '{$filter['documentDate']}' ";
        }

        if (isset($filter['documentNo'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.PK_employee_attendance_void = '{$filter['documentNo']}' ";
        }

        if (isset($filter['isVoided'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            if (is_array($filter['isVoided'])) {
                $values = implode(', ', $filter['isVoided']);
                $where .= " a.isVoided IN ({$values}) ";
            } else {
                $where .= " a.isVoided = '{$filter['isVoided']}' ";
            }
        }

        if (isset($filter['isPosted'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            if (is_array($filter['isPosted'])) {
                $values = implode(', ', $filter['isPosted']);
                $where .= " a.isPosted IN ({$values}) ";
            } else {
                $where .= " a.isPosted = '{$filter['isPosted']}' ";
            }
        }

        if (isset($filter['isCancelled'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            if (is_array($filter['isCancelled'])) {
                $values = implode(', ', $filter['isCancelled']);
                $where .= " a.isCancelled IN ({$values}) ";
            } else {
                $where .= " a.isCancelled = '{$filter['isCancelled']}' ";
            }
        }

        // Pagination
        $pagination = "";
        if (isset($filter['page']) && isset($filter['limit'])) {
            $offset = ((intval($filter['page']) - 1 ) * $filter['limit']);
            $pagination = "LIMIT {$filter['limit']} OFFSET {$offset}";
        }

        $query = "
            SELECT
                a.PK_employee_attendance_void
                , DATE_FORMAT(a.createDate, \"%Y-%m-%d\") AS documentDate
                , CONCAT(b.lastName, \", \", b.firstName, \" \", b.middleName) AS `employeeName`
                , CASE
                    WHEN a.isVoided = 1 THEN 'voided'
                    WHEN a.isCancelled = 1 THEN 'cancelled'
                    WHEN a.isPosted = 1 THEN 'posted'
                    ELSE 'saved'
                END AS documentStatus
            FROM employee_attendance_void AS a
            INNER JOIN employees AS b ON a.FK_employee = b.PK_employee
            {$where}
            ORDER BY b.lastName, b.firstName, b.middleName
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