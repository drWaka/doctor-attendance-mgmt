<?php

class QuestionMstr {

    public static function index() {
        $query = "
            SELECT * FROM questionmstr
            ORDER BY title
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
            SELECT * FROM questionmstr
            WHERE PK_questionMstr = '{$id}'
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