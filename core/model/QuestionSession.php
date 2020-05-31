<?php

class QuestionSession {

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

    public static function getSessionByEmpDate($details) {
        $query = "
            SELECT a.PK_questionSession
            FROM questionSession AS a
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND a.FK_employee = '{$details['questionMstrId']}}'
                AND a.sessionDate BETWEEN '{$details['sessionDate']} 00:00:00' AND '{$details['sessionDate']} 23:59:59'
            LIMIT 1
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['PK_questionSession'];
        }

        return [];
    }

}