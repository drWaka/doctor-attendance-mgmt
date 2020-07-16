<?php

class QuestionResponse {

    public static function index() {

    }

    public static function create($details) {
        $query = "
            INSERT INTO questionresponse (
                FK_questionMstr, FK_questionSession, 
                FK_questionDtl, FK_employee
            ) VALUES (
                '{$details['questionMstrId']}', '{$details['questionSessionId']}', 
                '{$details['PK_questiondtl']}', '{$details['employeeId']}'
            )
        ";
        
        $response = array(
            "hasError" => 0,
            "errorMessage" => ''
        );

        if (!($GLOBALS['connection'] -> query($query))) {
            $response['hasError'] = 1;
            $response['errorMessage'] = 'Unable to generate survey questions. <br> Please contact your system administrator.';
        }

        return $response;
    }

    public static function show() {

    }

    public static function delete () {

    }

    public static function update() {

    }

    public static function updateResponse($details) {
        $responseRate = QuestionDtlOption::getRate(array(
            "questionDtlId" => $details['questionDtl'],
            "value" => $details['response']
        ));

        if (empty($responseRate) || $responseRate == '') {
            $responseRate = 0;
        }

        $query = "
            UPDATE questionresponse
            SET response = '{$details['response']}',
                rate = '{$responseRate}'
            WHERE FK_questionMstr = '{$details['questionMsrtId']}' 
                AND FK_questionSession = '{$details['questionSessionId']}' 
                AND FK_questionDtl = '{$details['questionDtl']}' 
                AND FK_employee = '{$details['employeeId']}'
        ";
        if (!($GLOBALS['connection'] -> query($query))) {
            return array(
                "error" => 1,
                "errorMessage" => 'Unable to initialize response. <br> Please conctact your system administrator'
            );
        }
        return array(
            "error" => 0,
            "errorMessage" => ''
        );
    }

    public static function getResponseByNo($details) {
        // die(var_dump($details['questionSessionId']));
        $query = "
            SELECT a.response
            FROM questionresponse AS a
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND a.FK_employee = '{$details['emplyeeId']}'
                AND a.FK_questionDtl = '{$details['questionDtlId']}'
                AND a.FK_questionSession = '{$details['questionSessionId']}'
            LIMIT 1
        ";

        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['response'];
        }

        return '';
    }

    public static function getTotalRate($sessionId) {
        $query = "
            SELECT SUM(a.rate) AS totalRate
            FROM questionresponse AS a
            WHERE a.FK_questionSession = '{$sessionId}'
        ";

        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['totalRate'];
        }

        return 0;
    }

}