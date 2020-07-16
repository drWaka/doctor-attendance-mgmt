<?php

class UserMstr {

    public static function index() {

    }

    public static function create() {

    }

    public static function show($id) {
        $query = "
            SELECT * FROM usermstr
            WHERE PK_userMstr = '{$id}'
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