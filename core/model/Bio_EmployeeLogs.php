<?php

class Bio_EmployeeLogs {

    public static function index() {
        return [];
    }

    public static function insert($data) {
        return false;
    }

    public static function show($id) {
        $query = "SELECT * FROM NGAC_AUTHLOG WHERE IndexKey = '{$id}'";
        $result = $GLOBALS['bioConnection'] -> prepare($query);
        $result -> execute();
        $rows = $result -> fetchAll();

        if (is_array($rows)) {
            if (count($rows) > 0) return $rows[0];
        }

        return null;
    }
    public static function update($data) {
        return false;
    }

    public static function delete ($id) {
        return false;
    }

    public static function filter($filter) {
        $where = "WHERE AuthResult = 0 ";
        if (isset($filter['fingerScanId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " UserID = '{$filter['fingerScanId']}' ";
        }

        if (isset($filter['dtrDate'])) {
            if (!is_array($filter['dtrDate'])) {
                $where .= (strlen($where) > 0) ? "AND" : "WHERE";
                $where .= " CONVERT(DATE, TransactionTime) = '{$filter['dtrDate']}' ";
            } else {
                $where .= (strlen($where) > 0) ? "AND" : "WHERE";
                $where .= " CONVERT(DATE, TransactionTime) BETWEEN '{$filter['dtrDate']['startDate']}' AND '{$filter['dtrDate']['endDate']}' ";
            }
        }

        if (isset($filter['excludedLogs'])) {
            if (is_array($filter['excludedLogs'])) {
                $excludedLogs = implode("', '", $filter['excludedLogs']);
                $where .= (strlen($where) > 0) ? "AND" : "WHERE";
                $where .= " IndexKey NOT IN ('{$excludedLogs}') ";
            } else {
                $where .= (strlen($where) > 0) ? "AND" : "WHERE";
                $where .= " IndexKey <> '{$filter['excludedLogs']}' ";
            }
        }

        if (isset($filter['includedLogs'])) {
            if (is_array($filter['includedLogs'])) {
                $includedLogs = implode("', '", $filter['includedLogs']);
                $where .= (strlen($where) > 0) ? "AND" : "WHERE";
                $where .= " IndexKey IN ('{$includedLogs}') ";
            } else {
                $where .= (strlen($where) > 0) ? "AND" : "WHERE";
                $where .= " IndexKey = '{$filter['includedLogs']}' ";
            }
        }

        $query = "SELECT * FROM NGAC_AUTHLOG {$where} ORDER BY TransactionTime DESC";
        $result = $GLOBALS['bioConnection'] -> prepare($query);
        $result -> execute();
        $rows = $result -> fetchAll();

        if (is_array($rows)) {
            if (count($rows) > 0) return $rows;
        }

        return [];
    }
}