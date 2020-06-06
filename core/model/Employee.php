<?php

class Employee {

    public static function index() {
        
    }

    public static function create() {

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

    public static function delete () {

    }

    public static function update() {

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