<?php

class Employee {

    public static function index() {
        
    }

    public static function insert($data) {
        $query = "
            INSERT INTO employees (
                firstName, middleName, lastName, 
                birthDate, gender, mobileNo, 
                AddressLine1, AddressLine2, AddressLine3, 
                employeeNo, email, FK_mscDivision,
                FK_mscDepartment
            ) VALUES (
                '{$data['firstName']}', '{$data['middleName']}', '{$data['lastName']}', 
                '{$data['birthDate']}', '{$data['gender']}', '{$data['mobileNo']}', 
                '{$data['addressLine1']}', '{$data['addressLine2']}', '{$data['addressLine3']}', 
                '{$data['employeeNo']}', '{$data['email']}', '{$data['FK_mscDivision']}', 
                '{$data['FK_mscDepartment']}'
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
        $query = "
            UPDATE employees
            SET firstName = '{$data['firstName']}',
                middleName = '{$data['middleName']}',
                lastName = '{$data['lastName']}',                
                birthDate = '{$data['birthDate']}',
                gender = '{$data['gender']}',
                mobileNo = '{$data['mobileNo']}',                
                AddressLine1 = '{$data['addressLine1']}',
                AddressLine2 = '{$data['addressLine2']}',
                AddressLine3 = '{$data['addressLine3']}',                
                employeeNo = '{$data['employeeNo']}',
                email = '{$data['email']}',
                FK_mscDivision = '{$data['FK_mscDivision']}',             
                FK_mscDepartment = '{$data['FK_mscDepartment']}'
            WHERE PK_employee = '{$data['PK_employee']}'
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function delete () {

    }

    public static function getByEmployeeNo($employeeNo) {
        $query = "
            SELECT * FROM employees
            WHERE employeeNo = '{$employeeNo}'
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function getByBirthdate($details) {
        $query = "
            SELECT * FROM employees
            WHERE PK_employee = '{$details['employeeId']}'
                AND birthDate = '{$details['birthdate']}' 
        ";
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