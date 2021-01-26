<?php

class Employee {

    public static function index() {
        $query = "SELECT * FROM employees ORDER BY lastName, firstName, middleName";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function insert($data) {
        $birthDate = (!empty($data['birthDate'])) ? "{$data['birthDate']}" : "NULL";
        $query = "
            INSERT INTO employees (
                firstName, middleName, lastName, 
                birthDate, gender, mobileNo, 
                AddressLine1, AddressLine2, AddressLine3, 
                employeeNo, email, FK_mscDepartment,
                clinic
            ) VALUES (
                '{$data['firstName']}', '{$data['middleName']}', '{$data['lastName']}', 
                {$birthDate}, '{$data['gender']}', '{$data['mobileNo']}', 
                '{$data['addressLine1']}', '{$data['addressLine2']}', '{$data['addressLine3']}', 
                '{$data['employeeNo']}', '{$data['email']}', '{$data['FK_mscDepartment']}', 
                '{$data['clinic']}'
            )
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function show($id) {
        $query = "
            SELECT * FROM employees
            WHERE PK_employee = '{$id}'
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return null;
    }
    public static function update($data) {
        $birthDate = (!empty($data['birthDate'])) ? "{$data['birthDate']}" : "NULL";
        $query = "
            UPDATE employees
            SET firstName = '{$data['firstName']}',
                middleName = '{$data['middleName']}',
                lastName = '{$data['lastName']}',                
                birthDate = {$birthDate},
                gender = '{$data['gender']}',
                mobileNo = '{$data['mobileNo']}',                
                AddressLine1 = '{$data['addressLine1']}',
                AddressLine2 = '{$data['addressLine2']}',
                AddressLine3 = '{$data['addressLine3']}',                
                employeeNo = '{$data['employeeNo']}',
                email = '{$data['email']}',             
                FK_mscDepartment = '{$data['FK_mscDepartment']}',             
                clinic = '{$data['clinic']}'
            WHERE PK_employee = '{$data['PK_employee']}'
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function setInactive($id) {
        $query = "UPDATE employees SET isDeleted = 1 WHERE PK_employee = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) return true;

        return false;
    }

    public static function delete ($id) {
        $query = "DELETE FROM employees WHERE PK_employee = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function getByEmployeeNo($employeeNo) {
        return self::filter(array(
            "employeeNo" => $employeeNo
        ));
    }

    public static function getByBirthdate($details) {
        return self::filter(array(
            "employeeId" => $details['employeeId'],
            "birthdate" => $details['birthdate']
        ));
    }

    public static function getByDepartment($departmentId) {
        return self::filter(array(
            "departmentId" => $departmentId
        ));
    }

    public static function filter($filter) {
        $where = "";
        if (isset($filter['employeeId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " PK_employee = '{$filter['employeeId']}' ";
        }

        if (isset($filter['birthdate'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " birthDate = '{$filter['birthdate']}' ";
        }

        if (isset($filter['employeeNo'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " employeeNo = '{$filter['employeeNo']}' ";
        }

        if (isset($filter['departmentId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " FK_mscDepartment = '{$filter['departmentId']}' ";
        }

        if (!isset($filter['isDeleted'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " isDeleted = 0 ";
        } else {
            if (!empty($filter['isDeleted'])) {
                $where .= (strlen($where) > 0) ? "AND" : "WHERE";
                $where .= " isDeleted = '{$filter['isDeleted']}' ";
            }
        }

        $query = "SELECT * FROM employees {$where}";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function getWithPagination($details) {
        $offset = ((intval($details['page']) - 1 ) * $details['limit']);
        $query = "
            SELECT * FROM employees
            ORDER BY lastName, firstName, middleName
            LIMIT {$details['limit']} OFFSET {$offset}
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

}