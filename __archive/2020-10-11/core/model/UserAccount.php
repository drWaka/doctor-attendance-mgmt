<?php

class UserAccount {

    public static function index() {

    }

    public static function create($details) {
        $salt = sha1(uniqid());
        $password = sha1($details['userName'] . $salt);
        $query = "
            INSERT INTO useracc(
                user_id, pwd, pwd_salt, 
                FK_userMstr, FK_userType
            ) VALUES (
                '{$details['userName']}', '{$password}', '{$salt}',
                '{$details['userId']}', '{$details['userTypeId']}'
            )
        ";
        // die($query);
        if ($GLOBALS['connection'] -> query($query)) {
            return $GLOBALS['connection'] -> insert_id;
        }

        return false;
    }

    public static function show($id) {
        $query = "SELECT * FROM useracc WHERE PK_userAcc = '{$id}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }
        
       return [];
    }

    public static function delete($id) {
        $query = "DELETE FROM useracc WHERE PK_userAcc = '{$id}'";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function update($details) {
        $query = "
            UPDATE useracc
            SET FK_userType = '{$details['userTypeId']}',
                isActive = '{$details['isActive']}'
        ";
        if (isset($details['password']) && !empty($details['password'])) {
            $query .= "
                , pwd = '{$details['password']}'
            ";
        }
        $query .= "
            WHERE PK_userAcc = '{$details['userAccId']}'
        ";
        if ($GLOBALS['connection'] -> query($query)) {
            return true;
        }
        return false;
    }

    public static function getByLogin($details, $activeOnly = 1) {
        $query = "
            SELECT * FROM useracc
            WHERE user_id = '{$details['userId']}'
        ";
        // die($query);

        if ($activeOnly == 1) {
            $query .= "AND isActive = 1 ";
        }
        if (isset($details['password']) && !empty($details['password'])) {
            // If the Password is included
            $salt = self::getPasswordSalt($details['userId']);
            $passwordHash = sha1($details['password'] . $salt);
            $query .= " AND pwd = '{$passwordHash}'";
        }
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function getPasswordSalt($details) {
        $query = "SELECT * FROM useracc WHERE user_id = '{$details}'";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $record = $result -> fetch_assoc();


            return $record['pwd_salt'];
        }

        return '';
    }

    public static function getUserMasterlist($filter = '') {
        $query = "
            SELECT
                a.PK_userMstr
                , a.lname
                , a.fname
                , a.email
                , c.PK_userType
                , c.description AS userType
                , b.PK_userAcc
                , b.user_id
                , CASE b.isActive
                    WHEN 1 THEN 'Active'
                    ELSE 'Inactive'
                END AS accountStatus
                , b.isActive
            FROM usermstr AS a
            INNER JOIN useracc AS b ON a.PK_userMstr = b.FK_userMstr
            INNER JOIN usertype AS c ON b.FK_userType = c.PK_userType
        ";

        $hasFilter = 0;
        if (isset($filter['userName_id'])) {
            $query .= ($hasFilter == 0) ? 'WHERE ' : 'AND ';
            $query .= "(
                CONCAT(a.lname, \", \", a.fname) LIKE '%{$filter['userName_id']}%'
                OR CONCAT(a.fname, \" \", a.lname) LIKE '%{$filter['userName_id']}%'
                OR b.user_id LIKE '%{$filter['userName_id']}%'
            ) ";
            $hasFilter = 1;
        }

        if (isset($filter['userTypeId'])) {
            $query .= ($hasFilter == 0) ? 'WHERE ' : 'AND ';
            $userTypeIds = implode(', ', $filter['userTypeId']);
            $query .= "c.PK_userType IN ({$userTypeIds})";
            $hasFilter = 1;
        }

        if (isset($filter['userId'])) {
            $query .= ($hasFilter == 0) ? 'WHERE ' : 'AND ';
            $query .= "a.PK_userMstr = '{$filter['userId']}'";
            $hasFilter = 1;
        }
        $query .= "ORDER BY a.lname, a.fname, b.user_id";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            $rows = $result -> fetch_all(MYSQLI_ASSOC);
            return $rows;
        }

        return [];
    }

    public static function updatePassword($details) {
        $userAccount = self::getUserMasterlist(array("userId" => $details['userId']));
        $userAccount = self::show($userAccount[0]['PK_userAcc']);

        $salt = self::getPasswordSalt($userAccount[0]['user_id']);
        $password = sha1($details['password'] . $salt);
        if (self::update(array(
            "userTypeId" => $userAccount[0]['FK_userType'],
            "isActive" => $userAccount[0]['isActive'],
            "password" => $password,
            "userAccId" => $userAccount[0]['PK_userAcc']
        ))) {
            return true;
        }

        return false;
    }

}