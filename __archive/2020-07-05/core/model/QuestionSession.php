<?php

class QuestionSession {

    public static function index() {

    }

    public static function create($details) {
        $currentDatetime = date('Y-m-d h:i:s');
        $query = "
            INSERT INTO questionsession (FK_questionMstr, FK_employee, sessionDate)
            VALUES ('{$details['questionMstrId']}', '{$details['employeeId']}', '{$currentDatetime}')
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
            SELECT *
            FROM questionsession AS a
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND a.FK_employee = '{$details['employeeId']}'
                AND a.sessionDate BETWEEN '{$details['sessionDate']} 00:00:00' AND '{$details['sessionDate']} 23:59:59'
            LIMIT 1
        ";

        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return [];
    }

    public static function hasSession($details) {
        $sessionDateRange = array(
            "start" => '',
            "end" => ''
        );
        if (is_array($details['sessionDate'])) {
            $sessionDateRange['start'] = $details['sessionDate']['start'];
            $sessionDateRange['end'] = $details['sessionDate']['end'];
        } else {
            $sessionDateRange['start'] = $details['sessionDate'];
            $sessionDateRange['end'] = $details['sessionDate'];
        }

        $query = "
            SELECT * FROM questionsession
            WHERE sessionDate BETWEEN '{$sessionDateRange['start']} 00:00:00' AND '{$sessionDateRange['end']} 23:59:59' 
                AND FK_employee = '{$details['employeeId']}'
                AND FK_questionMstr = '{$details['questionMsrtId']}'
            LIMIT 1
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function finalizeSession($details) {
        $response = array(
            "isSuccess" => 1,
            "errorMessage" => ''
        );
        $query = "
            UPDATE questionsession
            SET isDone = 1,
                totalRate = '{$details['totalRate']}',
                remarks = '{$details['remarks']}'
            WHERE PK_questionSession = '{$details['questionSessionId']}'
        ";
        // die($query);
        if (!$GLOBALS['connection'] -> query($query)) {
            $response['isSuccess'] = 0;
            $response['errorMessage'] = 'Unable to finalize Survey Session. <br> Please contact your system administrator';
        }

        return $response;
    }
}