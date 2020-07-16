<?php

class UserAccount {

    public static function index() {

    }

    public static function create() {

    }

    public static function show() {
       
    }

    public static function delete () {

    }

    public static function update() {

    }

    public static function getByLogin($details) {
        $query = "
            SELECT * FROM useracc
            WHERE user_id = '{$details['userId']}'
                AND isActive = 1
        ";
        // die($query);
        if (isset($details['password']) && !empty($details['password'])) {
            // If the Password is included
            $salt = self::getPasswordSalt($details['userId']);
            $passwordHash = sha1($details['password'] . $salt);
            $query .= " AND pwd = '{$passwordHash}'";

            // die($query);
        }

        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function getPasswordSalt($details) {
        $query = "SELECT * FROM useracc WHERE user_id = '{$details}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_assoc();

            return $record['pwd_salt'];
        }

        return '';
    }

}