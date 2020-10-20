<?php

class MscEmailNotif {
    public static function index() {

    }

    public static function create($details) {
        $query = "
            INSERT INTO mscemailnotif (
                FK_employee, FK_mscRecord, recordType
            ) VALUES (
                '{$details['FK_employee']}', '{$details['FK_mscRecord']}', '{$details['recordType']}'
            )
        ";

        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function show($id) {
        $query = "SELECT * FROM mscemailnotif WHERE PK_mscEmailNotif = '{$id}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public static function delete($id) {
        $query = "DELETE FROM mscemailnotif WHERE PK_mscEmailNotif = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function filter($filter) {
        $where = "";
        if (isset($filter['FK_mscRecord'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.FK_mscRecord = '{$filter['FK_mscRecord']}'";
        }

        if (isset($filter['recordType'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.recordType = '{$filter['recordType']}'";
        }

        if (isset($filter['PK_mscEmailNotif'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.PK_mscEmailNotif = '{$filter['PK_mscEmailNotif']}'";
        }

        if (isset($filter['employeeName'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " CONCAT(b.lastName, ', ', b.firstName, ' ', SUBSTRING(b.middleName, 1, 1), '.') LIKE '%{$filter['employeeName']}%'";
        }

        if (isset($filter['FK_employee'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.FK_employee = '{$filter['FK_employee']}'";
        }


        $query = "
            SELECT 
                a.PK_mscEmailNotif
                , CONCAT(b.lastName, ', ', b.firstName, ' ', SUBSTRING(b.middleName, 1, 1), '.') AS `employeeName`
                , IFNULL(b.email, '-') AS `email`
            FROM mscemailnotif AS a
            INNER JOIN employees AS b ON a.FK_employee = b.PK_employee
            {$where}
            ORDER BY CONCAT(b.lastName, ', ', b.firstName, ' ', b.middleName)
        ";

        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }


    // For Future Reference
    // public static function where($filter) {
    //     $where = "";
    //     foreach($filter as $row) {
    //         if (is_array($row[0])) {
    //             $where .= "(";
    //             foreach($row as $subRow) {

    //             }
    //             $where .= ")";
    //         } else {
    //             $where .= (strlen($where) > 0) ? 'WHERE' : $row[0]; 
    //             switch (strtolower($filter[2])) {
    //                 case '>':
    //                 case '<':
    //                 case '>=':
    //                 case '<=':
    //                 case '!=':
    //                 case '=':
                        
                    
                    
    //                     break;
    //                 case 'AND':
                        
    //                     break;
                    
    //                 default:
    //                     # code...
    //                     break;
    //             }
    //         }
    //     }

    //     $query = "SELECT * FROM mscemailnotif {$where}";
        
    // }
}