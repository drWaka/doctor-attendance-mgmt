<?php

class QuestionRating {

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

    public static function getRemarksByRate($details) {
        $query = "
            SELECT * FROM questionrating
            WHERE '{$details['rate']}' BETWEEN minrate AND maxrate 
                AND FK_questionMstr = '{$details['questionMstrId']}'
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_assoc();
            return $record['description'];
        }

        return '';
    }

}