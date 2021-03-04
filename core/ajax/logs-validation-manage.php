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

if (
    isset($_POST['recordId']) && isset($_POST['documentNo']) && 
    isset($_POST['documentDate']) && isset($_POST['employeeId']) && 
    isset($_POST['employeeName'])
) {
    $recordId = new form_validation($_POST['recordId'], 'str-int', 'Record ID', true);
    $documentNo = new form_validation($_POST['documentNo'], 'str-int', 'Document No', true);
    $documentDate = new form_validation($_POST['documentDate'], 'date', 'Document Date', true);
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Doctor ID', true);
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Doctor Name', true);

    if (
        $recordId -> valid == 1 && $documentNo -> valid == 1 && 
        $documentDate -> valid == 1 && $employeeId -> valid == 1 && 
        $employeeName -> valid == 1
    ) {
        // Verify if the Employee ID is valid
        $employee = Employee::show($employeeId -> value);
        if (is_null($employee)) {
            $employeeId -> valid = 0;
            $employeeId -> err_msg = "Employee Record not found";
        }
    }

    if (
        $recordId -> valid == 1 && $documentNo -> valid == 1 && 
        $documentDate -> valid == 1 && $employeeId -> valid == 1 && 
        $employeeName -> valid == 1
    ) {
        // Verify if the Employee ID has an existing pending Document Record
        $logValidationRec = EmployeeAttendanceVoid::filter(array(
            "FK_employee" => $employeeId -> value,
            "documentDate" => $documentDate -> value,
            "isVoided" => '0',
            "isPosted" => '0',
            "isCancelled" => '0'
        ));
        if (is_array($logValidationRec)) {
            if (count($logValidationRec) > 0) {
                $employeeName -> valid = 0;
                $employeeName -> err_msg = "Pending record for the selected employee exists";
            }
        }
    }

    if (
        $recordId -> valid == 1 && $documentNo -> valid == 1 && 
        $documentDate -> valid == 1 && $employeeId -> valid == 1 && 
        $employeeName -> valid == 1
    ) {
        $modalLbl = array(
            "present" => 'Adding',
            "past" => 'Added',
            "future" => 'Add'
        );

        $isSuccess = EmployeeAttendanceVoid::insert(array(
            "FK_employee" => $employeeId -> value,
            "FK_user_create" => $_SESSION['userId'],
            "createDate" => $documentDate -> value
        ));

        if ($isSuccess) {
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Doctor Schedule ' . $modalLbl['present'] . ' Success</h2>
                    <p class="para-text">Doctor Schedule ' . $modalLbl['past'] . ' Successfully</p>
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
                    <p class="para-text col-12">Error Details: Unable to Add Logs Validation Document <br> Please contact your system administrator. </p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        if ($recordId -> valid == 0) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$recordId -> err_msg}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        } else {
            $recordIdErr = new error_handler($recordId -> err_msg);
            $documentNoErr = new error_handler($documentNo -> err_msg);
            $documentDateErr = new error_handler($documentDate -> err_msg);

            $employeeIdErr = new error_handler($employeeId -> err_msg);
            $employeeNameErr = new error_handler($employeeName -> err_msg);
            
            $employeeErr = new stdClass();
            if ($employeeId -> valid == 0) $employeeErr = $employeeIdErr;
            if ($employeeName -> valid == 0) $employeeErr = $employeeNameErr;
            
            
            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Log Validation Record Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 logs-validation-form">
                        <form form-name="respondent-form" action="../core/ajax/logs-validation-manage.php" tran-type="async-form">
                            <input type="text" name="recordId" hidden="hidden" value="' . $recordId -> value . '">
    
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Document No. : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $documentNoErr -> error_class . '" name="documentNo" placeholder="Document No" value="' . $documentNo -> value . '" readonly>
                                            ' . $documentNoErr -> error_icon . '
                                            ' . $documentNoErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Document Date : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="date" class="form-control ' . $documentDateErr -> error_class . '" name="documentDate" value="' . $documentDate -> value . '" readonly>
                                            ' . $documentDateErr -> error_icon . '
                                            ' . $documentDateErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Doctor Name : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control" name="employeeId"  value="' . $employeeId -> value . '" hidden>
                                            <input type="text" class="form-control ' . $employeeErr -> error_class . '" name="employeeName" placeholder="Doctor Name" value="' . $employeeName -> value . '" autocomplete="off">
                                            ' . $employeeErr -> error_icon . '
                                            ' . $employeeErr -> error_text . '
                                            <div class="search-result">
                    
                                                <div class="search-result-item row">
                                                    <i class="">Start typing to search a doctor...</i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'regular',
                    "btnLbl" => 'Submit'
                )
            );
        }
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