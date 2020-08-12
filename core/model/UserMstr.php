<?php

class UserMstr {

    public static function index() {
        $query = "SELECT * FROM usermstr ORDER BY lname, fname";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $rows = $result -> fetch_all(MYSQLI_ASSOC);
            return $rows;
        }

        return [];
    }

    public static function create($details) {
        $query = "INSERT INTO usermstr(fname, lname, email) VALUES ('{$details['firstName']}', '{$details['lastName']}', '{$details['email']}')";
        if ($GLOBALS['connection'] -> query($query)) {
            return $GLOBALS['connection'] -> insert_id;
        }

        return false;
    }

    public static function show($id) {
        $query = "
            SELECT * FROM usermstr
            WHERE PK_userMstr = '{$id}'
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function delete ($id) {
        $query = "DELETE FROM userMstr WHERE PK_userMstr = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function update($details) {
        $query = "
            UPDATE userMstr
            SET fname = '{$details['firstName']}', 
                lname = '{$details['lastName']}', 
                email = '{$details['email']}'
            WHERE PK_userMstr = '{$details['PK_userMstr']}'
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }
    

}