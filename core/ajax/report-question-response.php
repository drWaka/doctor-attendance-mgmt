<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => '',
        "record" => '',
        "total" => 0
    ),
    "contentType" => ''
);

// echo var_dump(isset($_POST['employeeName']));
// echo var_dump(isset($_POST['sessionDate']));
// echo var_dump(isset($_POST['questionMstrId']));
// echo var_dump(isset($_POST['pageLimit']));
// echo var_dump(isset($_POST['currentPage']));

// die();

if (
    isset($_POST['employeeName']) && 
    isset($_POST['sessionDate']) && 
    isset($_POST['questionMstrId']) &&
    isset($_POST['departmentId']) &&
    isset($_POST['divisionId']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $questionMstrId = new form_validation($_POST['questionMstrId'], 'int', 'Question Master ID', true);
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Employee Name', false);
    $sessionDate = new form_validation($_POST['sessionDate'], 'date', 'Session Date', true);
    $departmentId = new form_validation($_POST['departmentId'], 'str-int', 'Department ID', true);
    $divisionId = new form_validation($_POST['divisionId'], 'str-int', 'Division ID', true);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if (
        $questionMstrId -> valid == 1 && $employeeName -> valid == 1 && $sessionDate -> valid == 1 && $departmentId -> valid == 1 && $divisionId -> valid == 1 &&
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
    ) {
        // Determine if the Question Master Id exists
        $question = QuestionMstr::show($questionMstrId -> value);

        if (!(count($question) > 0)) {
            $questionMstrId -> valid = 0;
            $questionMstrId -> err_msg = 'Question Master ID is invalid';
        }
    }

    if (
        $questionMstrId -> valid == 1 && $employeeName -> valid == 1 && $sessionDate -> valid == 1 && $departmentId -> valid == 1 && $divisionId -> valid == 1 &&
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
    ) {
        $employeeQuery = "
            SELECT a.* 
            FROM employees AS a
            LEFT OUTER JOIN mscdepartment AS b ON a.FK_mscdepartment = b.PK_mscDepartment
            LEFT OUTER JOIN mscdivision AS c ON a.FK_mscdivision = c.PK_mscdivision
            WHERE 
                (CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), '. ', lastName) LIKE '%{$employeeName -> value}%'

                OR CONCAT(lastName, ', ', firstName, ' ', middleName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName, ' ', SUBSTR(middleName, 1, 1)) LIKE '%{$employeeName -> value}%'
                OR employeeNo LIKE '%{$employeeName -> value}%')
        ";
        if ($departmentId -> value != 'all' && is_numeric($departmentId -> value)) {
            $employeeQuery .= "AND b.PK_mscDepartment = '{$departmentId -> value}'";
        }

        if ($divisionId -> value != 'all' && is_numeric($divisionId -> value)) {
            $employeeQuery .= "AND c.PK_mscDivision = '{$divisionId -> value}'";
        }
        // die($employeeQuery);
        $employeeResult = $connection -> query($employeeQuery);
        $response['content']['total'] = $employeeResult -> num_rows;
        
        $offset = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
        $employeeQuery .= "LIMIT {$pageLimit -> value} OFFSET {$offset}";
        $employeeResult = $connection -> query($employeeQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_ASSOC);

        $response['content']['record'] = '';
        if (count($employeeRecords) > 0) {
            foreach ($employeeRecords as $employeeRecord) {
                $middleInitial = !empty($employeeRecord['middleName'])
                    ? substr($employeeRecord['middleName'], 0, 1)
                    : '';
                $employeeName = strtoupper(("{$employeeRecord['lastName']}, {$employeeRecord['firstName']} {$middleInitial}."));

                $response['content']['record'] .= "<tr>";
                $response['content']['record'] .= "
                    <td>{$employeeRecord['employeeNo']}</td>
                    <td>{$employeeName}</td>
                ";
                // Get Response Summary
                $questionSession = QuestionSession::getSessionByEmpDate(array(
                    "employeeId" => $employeeRecord['PK_employee'],
                    "questionMstrId" => $questionMstrId -> value,
                    "sessionDate" => $sessionDate -> value
                ));

                if (count($questionSession) > 0) {
                    $sessionDateVal = date('F d, Y', strtotime($questionSession['sessionDate']));
                    $response['content']['record'] .= "
                        <td>{$sessionDateVal}</td>
                        <td>{$questionSession['remarks']}</td>
                        <td><button class='btn btn-default'><i class='fa fa-eye'></i></button></td>
                    ";
                } else {
                    // No Response Summary Found
                    $response['content']['record'] .= "
                        <td class='text-center' colspan='3'>No Response</td>
                    ";
                }
                $response['content']['record'] .= "<tr>";
            }
        } else {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] = '<tr><td class="text-center" colspan="5">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($questionMstrId -> valid == 0) {
            $errorMessage = $questionMstrId -> err_msg;
        } else if ($employeeName -> valid == 0) {
            $errorMessage = $employeeName -> err_msg;
        } else if ($sessionDate -> valid == 0) {
            $errorMessage = $sessionDate -> err_msg;
        } else if ($departmentId -> valid == 0) {
            $errorMessage = $departmentId -> err_msg;
        } else if ($divisionId -> valid == 0) {
            $errorMessage = $divisionId -> err_msg;
        } else if ($pageLimit -> valid == 0) {
            $errorMessage = $pageLimit -> err_msg;
        } else if ($currentPage -> valid == 0) {
            $errorMessage = $currentPage -> err_msg;
        }

        $response['success'] = 'failed';
        $response['contentType'] = 'modal';
        $response['content']['modal'] = modalize(
            "<div class='row text-center'>
                <h2 class='header capitalize col-12'>System Error Encountered</h2>
                <p class='para-text col-12'>Error Details: {$errorMessage}</p>
            </div>", 
            array(
                "trasnType" => 'error',
                "btnLbl" => 'Dismiss'
            )
        );  
    }

} else {
    $response['success'] = 'failed';
    $response['contentType'] = 'modal';
    $response['content']['modal'] = modalize(
        '<div class="row text-center">
            <h2 class="header capitalize col-12">System Error Encountered</h2>
            <p class="para-text col-12">Error Details: Insufficient Data Submitted<br/> Please contact your System Administrator</p>
        </div>', 
        array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
        )
    );   
}


// Encode JSON Response
encode_json_file($response);