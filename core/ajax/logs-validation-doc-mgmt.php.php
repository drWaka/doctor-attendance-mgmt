<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => ''
    ),
    "contentType" => 'modal'
);

if (isset($_POST['recordId']) && isset($_POST['transType'])) {
    $recordId = new form_validation($_POST['recordId'], 'int', 'Record ID', true);
    $transType = new form_validation($_POST['transType'], 'str', 'Transaction Type', true);

    if ($recordId -> valid == 1 && $transType -> valid == 1) {
        // Verify Log Validation Record
        $logValidationRecord = EmployeeAttendanceVoid::show($recordId -> value);

        $recordId -> valid = 0;
        $recordId -> err_msg = "Log Validation Document not found";

        if (is_array($logValidationRecord)) {
            if (count($logValidationRecord) > 0) {
                $recordId -> valid = 1;
                $recordId -> err_msg = "";
            }
        }
    }

    if ($recordId -> valid == 1 && $transType -> valid == 1) {
        // Verify Transaction Type
        if (strtolower($transType -> value) != 'post' && strtolower($transType -> value) != 'cancel' && strtolower($transType -> value) != 'void') {
            $transType -> valid = 0;
            $transType -> err_msg = "Transaction Type is Invalid";
        }
    }

    if ($recordId -> valid == 1 && $transType -> valid == 1) {
        // If Transaction is "POST" ensure that the document is not empty
        if (strtolower($transType -> value) == 'post') {
            $includedLogs = EmployeeAttendanceVoidContent::filter(array(
                "logValidationId" => $recordId -> value
            ));
            if (is_array($includedLogs)) {
                if (count($includedLogs) == 0) {
                    $recordId -> valid = 0;
                    $recordId -> err_msg = "Posting empty Log Validation Document is not allowed";
                }
            }
        }
    }

    if ($recordId -> valid == 1 && $transType -> valid == 1) {
        $modalLbl = array();
        $result = '';
        
        if ($transType -> value == 'post') {
            $modalLbl = array(
                "present" => 'Posting',
                "past" => 'Posted',
                "future" => 'Post'
            );
            $result = EmployeeAttendanceVoid::post(array(
                "recordId" => $recordId -> value,
                "transDate" => date('Y-m-m'),
                "FK_user" => $_SESSION['userId']
            ));
        } else if ($transType -> value == 'cancel') {
            $modalLbl = array(
                "present" => 'Cancellation',
                "past" => 'Cancelled',
                "future" => 'Cancel'
            );
            $result = EmployeeAttendanceVoid::cancel(array(
                "recordId" => $recordId -> value,
                "transDate" => date('Y-m-m'),
                "FK_user" => $_SESSION['userId']
            ));
        } else if ($transType -> value == 'void') {
            $modalLbl = array(
                "present" => 'Voiding',
                "past" => 'Voided',
                "future" => 'Void'
            );
            $result = EmployeeAttendanceVoid::void(array(
                "recordId" => $recordId -> value,
                "transDate" => date('Y-m-m'),
                "FK_user" => $_SESSION['userId']
            ));
        }

        if ($result) {
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Log Validation Document ' . $modalLbl['present'] . ' Success</h2>
                    <p class="para-text">Log Validation Document ' . $modalLbl['past'] . ' Successfully</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'btn-trigger',
                    "btnLbl" => 'OK',
                )
            );
        } else {
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize col-12">Error Encountered</h2>
                    <p class="para-text col-12">Error Details: Unable to ' . $modalLbl['future'] . ' Log Validation Document</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        $errorMessage = '';
        if ($recordId -> valid == 0) {
            $errorMessage = $recordId -> err_msg;
        } else if ($transType -> valid == 0) {
            $errorMessage = $transType -> err_msg;
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