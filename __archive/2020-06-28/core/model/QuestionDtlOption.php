<?php

class QuestionDtlOption {

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

    public static function getByQuestionId($questionId) {
        $query = "
            SELECT *
            FROM questiondtloption AS a
            WHERE a.FK_questionDtl = '{$questionId}'
            ORDER BY a.sorting
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function checkRedirection($details) {
        $response = array(
            "hasRedirection" => 0,
            "pageNo" => '',
            "groupNo" => ''
        );
        $query = "
            SELECT *
            FROM questiondtloption AS a
            WHERE a.FK_questionDtl = '{$details['questionDtlId']}'
                AND a.value = '{$details['responseVal']}'
                AND a.hasRedirection = 1
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            $response['hasRedirection'] = $record[0]['hasRedirection'];
            $response['pageNo'] = $record[0]['redirectionPage'];
            $response['groupNo'] = $record[0]['redirectionGroup'];
        }

        return $response;
    }

    public static function getRate($details) {
        $query = "
            SELECT *
            FROM questiondtloption AS a
            WHERE a.FK_questionDtl = '{$details['questionDtlId']}'
                AND a.value = '{$details['value']}'
            LIMIT 1
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record = $result -> fetch_assoc();
            return $record['rate'];
        }

        return '';
    }
}