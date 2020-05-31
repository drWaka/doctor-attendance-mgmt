<?php

class QuestionSession {

    public static function index() {

    }

    public static function create($details) {
        $query = "
            INSERT INTO questionsession (FK_questionMstr, FK_employee)
            VALUES ('{$details['questionMstrId']}', '{$details['employeeId']}')
        ";

        $response = array(
            "errorMessage" => '',
            "hasError" => 0
        );

        if (!($GLOBALS['connection'] -> query($query))) {
            $response['errorMessage'] = 1;
            $response['errorMessage'] = 'Unable to initialize survey session. <br> Please conctact your system administrator';
        }

        return $response;
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
            FROM questionsession AS a
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND a.FK_employee = '{$details['employeeId']}'
                AND a.sessionDate BETWEEN '{$details['sessionDate']} 00:00:00' AND '{$details['sessionDate']} 23:59:59'
            LIMIT 1
        ";

        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['PK_questionSession'];
        }

        return [];
    }

    public static function hasSession($details) {
        $query = "
            SELECT * FROM questionsession
            WHERE sessionDate BETWEEN '{$details['sessionDate']} 00:00:00' AND '{$details['sessionDate']} 23:59:59' 
                AND FK_employee = '{$details['employeeId']}'
            LIMIT 1
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

}