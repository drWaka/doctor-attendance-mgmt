<?php

class UserType {

    public static function index() {
        $query = "
            SELECT * FROM userType
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function create() {

    }

    public static function show($id) {
        $query = "
            SELECT * FROM usertype
            WHERE PK_userType = '{$id}'
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function delete () {

    }

    public static function update() {

    }

}