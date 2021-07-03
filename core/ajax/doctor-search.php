<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "httpStatus" => 'success',
    "type" => 'element',
    "content" => ''
);

if (isset($_POST['employeeName'])) {
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Employee Name', false);

    if ($employeeName -> valid == 1) {
        $employeeQuery = "
            SELECT 
                PK_employee AS `employeeId`
                , UPPER(CONCAT(a.lastName, \", \", a.firstName, \" \",  a.middleName)) AS `employeeName`
                , a.employeeNo
            FROM employees AS a
            WHERE 
                (CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), '. ', lastName) LIKE '%{$employeeName -> value}%'

                OR CONCAT(lastName, ', ', firstName, ' ', middleName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName, ' ', SUBSTR(middleName, 1, 1)) LIKE '%{$employeeName -> value}%')
                AND a.isDeleted <> 1
            ORDER BY a.lastName, a.firstName, a.middleName
            LIMIT 5
        ";
        // die($employeeQuery);
        $employeeResult = $connection -> query($employeeQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_ASSOC);

        if (count($employeeRecords) > 0) {
            $response['content'] = array(
                "total" => 0,
                "record" => []
            );
            $response['content']['total'] = count($employeeRecords);
            foreach ($employeeRecords as $employeeRecord) {
                $response['content']['record'][count($response['content']['record'])] = $employeeRecord;
            }
        }
    } else {
        $response['httpStatus'] = 'failed';
        $response['type'] = 'notif';
        $response['content'] = "Error Details: {$employeeName -> err_msg}";  
    }

} else {
    $response['httpStatus'] = 'failed';
    $response['type'] = 'notif';
    $response['content'] = 'Error Details: Insufficient Data Submitted<br/> Please contact your System Administrator';   
}


// Encode JSON Response
encode_json_file($response);