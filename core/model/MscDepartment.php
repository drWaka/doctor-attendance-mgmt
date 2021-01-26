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
        $query = "INSERT INTO mscdepartment(description, specialization) VALUES ('{$details['description']}', '{$details['specialization']}')";
        if ($GLOBALS['connection'] -> query($query)) return true;
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
                specialization = '{$details['specialization']}'
            WHERE PK_mscDepartment = '{$details['departmentId']}'
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function filter($filter) {
        $where = "";

        if (isset($filter['description'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.description LIKE '%{$filter['description']}%'";
        }
        $query = "
            SELECT 
                a.PK_mscDepartment
                , a.description AS department
                , a.specialization
            FROM mscdepartment AS a
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
                AND PK_mscDepartment != '{$filter['departmentId']}'
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
}