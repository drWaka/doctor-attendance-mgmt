<?php

class QuestionGrp {

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

    public static function getMaxGroupNo($questionMstrId) {
        $query = "
            SELECT a.sorting FROM questiongrp AS a
            WHERE a.FK_questionMstr = '{$questionMstrId}'
            ORDER BY a.sorting DESC LIMIT 1
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['sorting'];
        }

        return 0;
    }

}