<?php

class MscDepartment {

    public static function index() {
        
    }

    public static function create() {

    }

    public static function show($id) {
        $query = "
            SELECT * FROM mscdeparment
            WHERE PK_mscdeparment = '{$id}'
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

    public static function getByDivision($divisionId) {
        $query = "
            SELECT * 
            FROM mscdepartment 
            WHERE FK_mscdivision = '{$divisionId}'
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return null;
    }

}