<?php

class QuestionSession {

    public static function index() {

    }

    public static function create($details) {
        $currentDatetime = date('Y-m-d H:i:s');
        $query = "
            INSERT INTO questionsession (FK_questionMstr, FK_employee, sessionDate)
            VALUES ('{$details['questionMstrId']}', '{$details['employeeId']}', '{$currentDatetime}')
        ";

        $hospitalPassMailingStatus = SystemFeatures::isFeatureEnabled('MAIL_HOSP_PASS');
        if ($hospitalPassMailingStatus == false) {
            $query = "
                INSERT INTO questionsession (FK_questionMstr, FK_employee, sessionDate, isMailed)
                VALUES ('{$details['questionMstrId']}', '{$details['employeeId']}', '{$currentDatetime}', 1)
            ";
        }

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

    public static function show($id) {
        $query = "SELECT * FROM questionsession WHERE PK_questionSession = '{$id}'";
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

    public static function filter($filter, $addition = '') {
        $where = "";
        if (isset($filter['questionMstrId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.FK_questionMstr = '{$filter['questionMstrId']}' ";
        }
        if (isset($filter['employeeId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.FK_employee = '{$filter['employeeId']}' ";
        }
        if (isset($filter['isDone'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.isDone = '{$filter['isDone']}' ";
        }
        if (isset($filter['sessionDate'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            if (isset($filter['sessionDate']['start']) && isset($filter['sessionDate']['end'])) {
                $where .= " a.sessionDate BETWEEN '{$filter['sessionDate']['start']}' AND '{$filter['sessionDate']['end']}'";
            } else {
                $where .= " a.sessionDate = '{$filter['sessionDate']}' ";
            }
        }

        $query = "SELECT * FROM questionsession AS a {$where} {$addition}";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return [];
    }

    public static function getSessionByEmpDate($details) {
        $sessionRecord = self::filter($details, 'LIMIT 1');
        if (count($sessionRecord) > 0) {
            return $sessionRecord[0];
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
                remarks = '{$details['remarks']}',
                finalizedDate = CURRENT_TIMESTAMP
            WHERE PK_questionSession = '{$details['questionSessionId']}'
        ";
        // die($query);
        if (!$GLOBALS['connection'] -> query($query)) {
            $response['isSuccess'] = 0;
            $response['errorMessage'] = 'Unable to finalize Survey Session. <br> Please contact your system administrator';
        }

        return $response;
    }

    public static function isSessionFinalized($id) {
        $sessionRecord = (self::show($id));

        if (count($sessionRecord) > 1) {
            if ($sessionRecord['isDone'] == 1) {
                return true;
            }
        }
        return false;
    }
}