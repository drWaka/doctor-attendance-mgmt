<?php

class MscDepartment {

    public static function index() {
        $query = "SELECT * FROM mscdepartment ORDER BY description";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public static function create($details) {
        $query = "
            INSERT INTO mscdepartment(
                description, FK_mscDivision
            ) VALUES (
                '{$details['description']}', '{$details['divisionId']}'
            )
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function show($id) {
        $query = "
            SELECT * FROM mscdepartment
            WHERE PK_mscdepartment = '{$id}'
            ORDER BY description
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return null;
    }

    public static function delete ($id) {
        $query = "DELETE FROM mscdepartment WHERE PK_mscDepartment = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function update($details) {
        $query = "
            UPDATE mscdepartment
            SET description = '{$details['description']}',
                FK_mscDivision = '{$details['divisionId']}'
            WHERE PK_mscDepartment = '{$details['departmentId']}'
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function getByDivision($divisionId) {
        $query = "
            SELECT * 
            FROM mscdepartment 
            WHERE FK_mscdivision = '{$divisionId}'
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return null;
    }

    public static function filter($filter) {
        $where = "";
        if (isset($filter['divisionId'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.FK_mscdivision = '{$filter['divisionId']}'";
        }

        if (isset($filter['description'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.description LIKE '%{$filter['description']}%'";
        }
        $query = "
            SELECT 
                a.PK_mscDepartment
                , a.description AS department
                , b.description AS division 
            FROM mscdepartment AS a
            INNER JOIN mscdivision AS b ON a.FK_mscDivision = b.PK_mscDivision
            {$where} 
            ORDER BY a.description
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return [];
    }

    public static function checkUnique($filter) {
        $query = "
            SELECT * 
            FROM mscdepartment 
            WHERE description = '{$filter['description']}'
                AND FK_mscDivision = '{$filter['divisionId']}'
                AND PK_mscDepartment != '{$filter['departmentId']}'
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
}