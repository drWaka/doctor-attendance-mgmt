<?php

class MscUnit {

    public static function index() {
        $query = "
            SELECT * FROM mscunit
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return [];
    }

    public static function create() {

    }

    public static function show($id) {
        $query = "
            SELECT * FROM mscunit
            WHERE PK_mscUnit = '{$id}'
            ORDER BY description
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

    public static function getByDepartment($departmentId) {
        $query = "SELECT * FROM mscunit WHERE FK_mscDepartment = '{$departmentId}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
}