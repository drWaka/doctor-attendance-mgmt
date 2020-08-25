<?php

class MscUnit {

    public static function index() {
        $query = "
            SELECT * FROM mscunit
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return [];
    }

    public static function create($details) {
        $query = "
            INSERT INTO mscunit(
                description, FK_mscDepartment
            ) VALUES (
                '{$details['description']}', '{$details['departmentId']}'
            )
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function show($id) {
        $query = "
            SELECT * FROM mscunit
            WHERE PK_mscUnit = '{$id}'
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return null;
    }

    public static function delete($id) {
        $query = "DELETE FROM mscunit WHERE PK_mscUnit = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function update($details) {
        $query = "
            UPDATE mscunit
            SET description = '{$details['description']}',
                FK_mscDepartment = '{$details['departmentId']}'
            WHERE PK_mscUnit = '{$details['unitId']}'
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function getByDepartment($departmentId) {
        $query = "SELECT * FROM mscunit WHERE FK_mscDepartment = '{$departmentId}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function checkUnique($filter) {
        return self::filter($filter);
    }

    public static function filter($filter) {
        $where = "";

        if (isset($filter['description'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.description LIKE '%{$filter['description']}%' ";
        }

        if (isset($filter['departmentId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " b.PK_mscDepartment = '{$filter['departmentId']}' ";
        }

        if (isset($filter['unitName'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.description = '{$filter['unitName']}' ";
        }
        if (isset($filter['unitId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.PK_mscUnit = '{$filter['unitId']}' ";
        }
        
        $query = "
            SELECT
                a.PK_mscUnit
                , a.description AS unit
                , b.description AS department 
            FROM mscunit AS a 
            INNER JOIN mscdepartment AS b ON a.FK_mscDepartment = b.PK_mscDepartment
            {$where}
            ORDER BY a.description
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}