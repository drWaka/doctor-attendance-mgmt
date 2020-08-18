<?php

class MscDivision {

    public static function index() {
        $query = "
            SELECT * FROM mscdivision
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record;
        }

        return null;
    }

    public static function create($details) {
        $query = "
            INSERT INTO mscdivision(description)
            VALUES ('{$details['description']}')
        ";

        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function show($id) {
        $query = "
            SELECT * FROM mscdivision
            WHERE PK_mscdivision = '{$id}'
            ORDER BY description
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0];
        }

        return null;
    }

    public static function delete ($id) {
        $query = "
            DELETE FROM mscdivision
            WHERE PK_mscdivision = '{$id}'
        ";

        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function update($details) {
        $query = "
            UPDATE mscdivision
            SET description = '{$details['description']}'
            WHERE PK_mscdivision = '{$details['PK_mscdivision']}'
        ";

        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }

        return false;
    }

    public static function searchByName($name = '', $isExact = false) {
        $query = "SELECT * FROM mscdivision";
        if (!empty($name)) {
            $where = " WHERE description LIKE '%{$name}%'";
            if ($isExact) {
                $where = " WHERE description = '{$name}'";
            }
            $query .= $where;
        }
        $query .= " ORDER BY description";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

}