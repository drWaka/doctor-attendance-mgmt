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

if (
    isset($_POST['employeeName']) && 
    isset($_POST['departmentId']) &&
    isset($_POST['isDeleted']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Employee Name', false);
    $departmentId = new form_validation($_POST['departmentId'], 'str-int', 'Department', true);
    $isDeleted = new form_validation($_POST['isDeleted'], 'str', 'Deleted Record Inclusion', true);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if (
        $employeeName -> valid == 1 && $departmentId -> valid == 1 && $isDeleted -> valid == 1 && 
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
     ) {
        $employeeQuery = "
            SELECT 
                a.*, 
                b.description AS `department`
            FROM employees AS a
            LEFT OUTER JOIN mscdepartment AS b ON a.FK_mscdepartment = b.PK_mscDepartment
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
        if ($isDeleted -> value == 'no') {
            $employeeQuery .= "AND a.isDeleted = 0 ";
        }

        if ($departmentId -> value != 'all' && is_numeric($departmentId -> value)) {
            $employeeQuery .= "AND b.PK_mscDepartment = '{$departmentId -> value}'";
        }

        // die($employeeQuery);
        $employeeResult = $connection -> query($employeeQuery);
        $response['content']['total'] = $employeeResult -> num_rows;
        
        $offset = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
        $employeeQuery .= "
            ORDER BY a.lastName, a.firstName, a.middleName
            LIMIT {$pageLimit -> value} OFFSET {$offset}
        ";
        $employeeResult = $connection -> query($employeeQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_ASSOC);

        $response['content']['record'] = '';
        if (count($employeeRecords) > 0) {
            foreach ($employeeRecords as $employeeRecord) {
                // Employee Name Field
                $middleInitial = !empty($employeeRecord['middleName'])
                    ? substr($employeeRecord['middleName'], 0, 1)
                    : '';
                $employeeName = utf8_encode(strtoupper(("{$employeeRecord['lastName']}, {$employeeRecord['firstName']} {$middleInitial}.")));

                $isDisabled = (boolVal($employeeRecord['isDeleted'])) ? 'disabled' : '';

                // Data Management Field
                $dataManagementBtn = "
                <button {$isDisabled} class='btn btn-success transaction-btn' title='Edit Respondent'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/doctor-select.php'
                    data-content='{
                        &quot;employeeId&quot; : &quot;{$employeeRecord['PK_employee']}&quot;
                    }'
                ><i class='fas fa-pencil-alt'></i></button>
                <button {$isDisabled} class='btn btn-danger transaction-btn' title='Delete Respondent'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/generic-warning-modal.php'
                    data-content='{
                        &quot;transType&quot;   : &quot;delete&quot;,
                        &quot;link&quot;        : &quot;../core/ajax/doctor-delete.php&quot;,
                        &quot;dataContent&quot; : {
                            &quot;recordId&quot;  : &quot;{$employeeRecord['PK_employee']}&quot;
                        },
                        &quot;headerTitle&quot; : &quot;Respondent&quot;
                    }'
                ><i class='fa fa-trash'></i></button>
                ";

                // Sched Management Field
                $schedMgmtBtn = "
                <button {$isDisabled} class='btn btn-success transaction-btn' title='Edit Respondent'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/doctor-sched-select.php'
                    data-content='{
                        &quot;employeeId&quot; : &quot;{$employeeRecord['PK_employee']}&quot;
                    }'
                ><i class='fas fa-eye'></i></button>
                ";

                $response['content']['record'] .= "<tr>";
                $response['content']['record'] .= "
                    <td>{$employeeRecord['employeeNo']}</td>
                    <td>{$employeeName}</td>
                    <td>{$employeeRecord['department']}</td>
                    <td class='text-center'>{$schedMgmtBtn}</td>
                    <td class='text-center'>{$dataManagementBtn}</td>
                ";
                $response['content']['record'] .= "<tr>";
            }
        } else {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] = '<tr><td class="text-center" colspan="5">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($employeeName -> valid == 0) {
            $errorMessage = $employeeName -> err_msg;
        } else if ($departmentId -> valid == 0) {
            $errorMessage = $departmentId -> err_msg;
        } else if ($isDeleted -> valid == 0) {
            $errorMessage = $isDeleted -> err_msg;
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