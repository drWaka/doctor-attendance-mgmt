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
    isset($_POST['docDate']) &&
    isset($_POST['docNo']) &&
    isset($_POST['isPosted']) &&
    isset($_POST['isCancelled']) && 
    isset($_POST['isVoided']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Doctor Name', false);
    $docDate = new form_validation($_POST['docDate'], 'date', 'Document No', true);
    $docNo = new form_validation($_POST['docNo'], 'int', 'Document No', false);
    $isPosted = new form_validation(boolVal($_POST['isPosted']), 'bool', 'Posted Records', false);
    $isCancelled = new form_validation(boolVal($_POST['isCancelled']), 'bool', 'Cancelled Records', false);
    $isVoided = new form_validation(boolVal($_POST['isVoided']), 'bool', 'Voided Records', false);
    
    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if (
        $employeeName -> valid == 1 && $docDate -> valid == 1 && $docNo -> valid == 1 && 
        $isPosted -> valid == 1 && $isCancelled -> valid == 1 && $isVoided -> valid == 1 && 
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
     ) {
        $where = "";
        if (!empty($employeeName -> value)) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " 
                (CONCAT(b.firstName, ' ', b.middleName, ' ', b.lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(b.firstName, ' ', b.lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(b.firstName, ' ', SUBSTR(b.middleName, 1, 1), ' ', b.lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(b.firstName, ' ', SUBSTR(b.middleName, 1, 1), '. ', b.lastName) LIKE '%{$employeeName -> value}%'

                OR CONCAT(b.lastName, ', ', b.firstName, ' ', b.middleName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(b.lastName, ', ', b.firstName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(b.lastName, ', ', b.firstName, ' ', SUBSTR(b.middleName, 1, 1)) LIKE '%{$employeeName -> value}%'
                OR b.employeeNo LIKE '%{$employeeName -> value}%') 
            ";
        }

        if (!empty($docDate -> value)) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " DATE_FORMAT(a.createDate, \"%Y-%m-%d\") = '{$docDate -> value}' ";
        }

        if (!empty($docNo -> value)) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " a.PK_employee_attendance_void = '{$docNo -> value}' ";
        }

        
        
        // Addt'l Status Filter
        $addtlStatFilter = '';
        // Posted Filter
        if ($isPosted -> value == true) {
            $addtlStatFilter .= " OR (
                a.isPosted = 1
                AND a.isVoided = 0
                AND a.isCancelled = 0
            ) ";
        }

        // Voided Filter
        if ($isVoided -> value == true) $addtlStatFilter .= " OR a.isVoided = 1 ";

        // Cancelled Filter
        if ($isCancelled -> value == true) $addtlStatFilter .= " OR a.isCancelled = 1 ";

        // Saved Filter
        $where .= (strlen($where) > 0) ? "AND" : "WHERE";
        $where .= " (
            (
                a.isPosted = 0
                AND a.isVoided = 0
                AND a.isCancelled = 0
            )
            {$addtlStatFilter}
        ) ";

        // Pagination
        $pagination = "";
        if (!empty($currentPage -> value) && !empty($pageLimit -> value)) {
            $offset = ((intval($currentPage -> value) - 1 ) * $pageLimit -> value);
            $pagination = "LIMIT {$pageLimit -> value} OFFSET {$offset}";
        }

        $query = "
            SELECT
                a.PK_employee_attendance_void
                , DATE_FORMAT(a.createDate, \"%Y-%m-%d\") AS documentDate
                , CONCAT(b.lastName, \", \", b.firstName, \" \", b.middleName) AS `employeeName`
                , CASE
                    WHEN a.isVoided = 1 THEN 'voided'
                    WHEN a.isCancelled = 1 THEN 'cancelled'
                    WHEN a.isPosted = 1 THEN 'posted'
                    ELSE 'saved'
                END AS documentStatus
            FROM employee_attendance_void AS a
            INNER JOIN employees AS b ON a.FK_employee = b.PK_employee
            {$where}
            ORDER BY b.lastName, b.firstName, b.middleName
        ";
        // die($query);
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $response['content']['total'] = $result -> num_rows;
        }

        $query .= $pagination;
        $voidedAttendanceRecords = [];
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $voidedAttendanceRecords = $result -> fetch_all(MYSQLI_ASSOC);
        }

        $response['content']['record'] = '';
        if (count($voidedAttendanceRecords) > 0) {
            foreach ($voidedAttendanceRecords as $voidedAttendanceRecord) {
                // Employee Name Field
                $employeeName = utf8_encode(strtoupper(($voidedAttendanceRecord['employeeName'])));

                // Data Management Field
                $disabled = array(
                    "post" => '',
                    "cancel" => '',
                    "void" => ''
                );
                if (strtolower($voidedAttendanceRecord['documentStatus']) == 'saved') {
                    $disabled['void'] = 'disabled';
                }

                if (strtolower($voidedAttendanceRecord['documentStatus']) == 'posted') {
                    $disabled['post'] = 'disabled';
                    $disabled['cancel'] = 'disabled';
                }

                if (strtolower($voidedAttendanceRecord['documentStatus']) == 'cancelled' || strtolower($voidedAttendanceRecord['documentStatus']) == 'voided') {
                    $disabled['post'] = 'disabled';
                    $disabled['cancel'] = 'disabled';
                    $disabled['void'] = 'disabled';
                }
                $docMgmtBtn = "
                    <button class='btn btn-info transaction-btn' title='Post Document' {$disabled['post']}
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/generic-warning-modal.php'
                        data-content='{
                            &quot;transType&quot;   : &quot;post&quot;,
                            &quot;link&quot;        : &quot;../core/ajax/logs-validation-doc-mgmt.php&quot;,
                            &quot;dataContent&quot; : {
                                &quot;recordId&quot; : &quot;{$voidedAttendanceRecord['PK_employee_attendance_void']}&quot;,
                                &quot;transType&quot; : &quot;post&quot;
                            },
                            &quot;headerTitle&quot; : &quot;Log Validation Document&quot;
                        }'
                    ><i class='fas fa-thumbtack'></i></button>
                    
                    <button class='btn btn-warning transaction-btn' title='Cancel Document' {$disabled['cancel']}
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/generic-warning-modal.php'
                        data-content='{
                            &quot;transType&quot;   : &quot;cancel&quot;,
                            &quot;link&quot;        : &quot;../core/ajax/logs-validation-doc-mgmt.php&quot;,
                            &quot;dataContent&quot; : {
                                &quot;recordId&quot; : &quot;{$voidedAttendanceRecord['PK_employee_attendance_void']}&quot;,
                                &quot;transType&quot; : &quot;cancel&quot;
                            },
                            &quot;headerTitle&quot; : &quot;Log Validation Document&quot;
                        }'
                    ><i class='fa fa-trash'></i></button>

                    <button class='btn btn-danger transaction-btn' title='Void Document' {$disabled['void']}
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/generic-warning-modal.php'
                        data-content='{
                            &quot;transType&quot;   : &quot;void&quot;,
                            &quot;link&quot;        : &quot;../core/ajax/logs-validation-doc-mgmt.php&quot;,
                            &quot;dataContent&quot; : {
                                &quot;recordId&quot; : &quot;{$voidedAttendanceRecord['PK_employee_attendance_void']}&quot;,
                                &quot;transType&quot; : &quot;void&quot;
                            },
                            &quot;headerTitle&quot; : &quot;Log Validation Document&quot;
                        }'
                    ><i class='fas fa-times'></i></button>
                ";

                // Logs Management Field
                $logsMgmtBtn = "
                    <button class='btn btn-success transaction-btn' title='Document Content'
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/logs-validation-bio-select.php'
                        data-content='{
                            &quot;recordId&quot; : &quot;{$voidedAttendanceRecord['PK_employee_attendance_void']}&quot;
                        }'
                    ><i class='fas fa-eye'></i></button>
                ";

                $response['content']['record'] .= "<tr>";
                $response['content']['record'] .= "
                    <td>{$voidedAttendanceRecord['PK_employee_attendance_void']}</td>
                    <td>{$voidedAttendanceRecord['documentDate']}</td>
                    <td>{$employeeName}</td>
                    <td class='text-center capitalize'>{$voidedAttendanceRecord['documentStatus']}</td>
                    <td class='text-center'>{$logsMgmtBtn}</td>
                    <td class='text-center'>{$docMgmtBtn}</td>
                ";
                $response['content']['record'] .= "<tr>";
            }
        } else {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] = '<tr><td class="text-center" colspan="6">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($employeeName -> valid == 0) {
            $errorMessage = $employeeName -> err_msg;
        } else if ($docDate -> valid == 0) {
            $errorMessage = $docDate -> err_msg;
        } else if ($docNo -> valid == 0) {
            $errorMessage = $docNo -> err_msg;
        } else if ($isPosted -> valid == 0) {
            $errorMessage = $isPosted -> err_msg;
        } else if ($isCancelled -> valid == 0) {
            $errorMessage = $isCancelled -> err_msg;
        } else if ($isVoided -> valid == 0) {
            $errorMessage = $isVoided -> err_msg;
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