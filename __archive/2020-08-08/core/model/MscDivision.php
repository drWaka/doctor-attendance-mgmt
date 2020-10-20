<?php

class MscDivision {

    public static function index() {
        $query = "
            SELECT * FROM mscdivision
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return null;
    }

    public static function create() {

    }

    public static function show($id) {
        $query = "
            SELECT * FROM mscdivision
            WHERE PK_mscdivision = '{$id}'
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

}