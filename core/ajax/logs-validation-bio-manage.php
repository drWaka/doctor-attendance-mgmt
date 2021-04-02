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

if (isset($_POST['recordId']) && isset($_POST['bioLogs'])) {
    $recordId = new form_validation($_POST['recordId'], 'int', 'Logs Validation Document ID', true);
    $bioLogs = new form_validation($_POST['bioLogs'], 'str-int', 'log to be included at the document', false);

    $inputLogs = '';
    if ($recordId -> valid == 1 && $bioLogs -> valid == 1) {
        $voidedAttendanceRecord = EmployeeAttendanceVoid::show($recordId -> value);
        if (is_null($voidedAttendanceRecord)) {
            $recordId -> valid = 0;
            $recordId -> err_msg = "Logs Validation Document doesn't exists";
        }
    }

    if ($recordId -> valid == 1 && $bioLogs -> valid == 1) {
        $inputLogs = explode(',', $bioLogs -> value);
        foreach ($inputLogs as $inputLog) {
            if (!filter_var($inputLog, FILTER_VALIDATE_INT)) {
                $bioLogs -> valid = 0;
                $bioLogs -> err_msg = 'Invalid Biometric Log ID Provided. Please try refreshing the page and try again. If issue still persist please contact your System Administrator';

                break;
            }
        }
    }

    if ($recordId -> valid == 1 && $bioLogs -> valid == 1) {
        $transactionSuccess = true;
        $errorMessage = '';
        $existingLogs = EmployeeAttendanceVoidContent::filter(array(
            "logValidationId" => $recordId -> value
        ));

        // Delete Excluded Logs
        foreach ($existingLogs as $existingLog) {
            $isExisting = array_filter($inputLogs, function($inputLog) {
                if ($inputLog == $GLOBALS['existingLog']['FK_biometric_log_id']) {
                    return true;
                }

                return false;
            });

            if (!(count($isExisting) > 0)) {
                if (!(EmployeeAttendanceVoidContent::delete($existingLog['PK_employee_attendance_void_content']))) {
                    $transactionSuccess = false;
                    $errorMessage = "Unable to remove Log No #{$existingLog['FK_biometric_log_id']} at the document. Please try refreshing the webpage. If issue still persists, please contact your system administrator";
                }
            }
        }

        if ($transactionSuccess) {
            // Add Newly Included Logs
            foreach ($inputLogs as $inputLog) {
                $isExisting = array_filter($existingLogs, function($existingLog) {
                    if ($GLOBALS['inputLog'] == $existingLog['FK_biometric_log_id']) {
                        return true;
                    }

                    return false;
                });

                if (!(count($isExisting) > 0)) {
                    if (!(EmployeeAttendanceVoidContent::insert(array(
                        "logValidationId" => $recordId -> value,
                        "bioTransactId" => $inputLog
                    )))) {
                        $transactionSuccess = false;
                        $errorMessage = "Unable to record Log No #{$inputLog} at the document. Please try refreshing the webpage. If issue still persists, please contact your system administrator";
                    }
                }
            }
        }

        if ($transactionSuccess) {
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Document Content Management Success</h2>
                    <p class="para-text">Document #' . $recordId -> value . ' Logs content has already been updated.</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'btn-trigger',
                    "btnLbl" => 'Okay, Thanks!'
                )
            );
        } else {
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
        $errorMessage = '';
        if ($recordId -> valid == 0) {
            $errorMessage = $recordId -> err_msg;
        } else if ($bioLogs -> valid == 0) {
            $errorMessage = $bioLogs -> err_msg;
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