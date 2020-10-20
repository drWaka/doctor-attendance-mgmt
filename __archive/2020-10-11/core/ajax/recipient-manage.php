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
    isset($_POST['referenceRecordId']) && isset($_POST['recordType']) && isset($_POST['employeeId'])
) {
    $referenceRecordId = new form_validation($_POST['referenceRecordId'], 'int', 'Rereference Record ID', true);
    $recordType = new form_validation($_POST['recordType'], 'str', 'Record Type', true);
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Employee ID', true);

    $flags = array(
        'hasError' => 0,
        'isDone' => 0
    );

    if ($referenceRecordId -> valid == 1 && $recordType -> valid == 1 && $employeeId -> valid == 1) {
        if ($recordType -> value != 'division' && $recordType -> value != 'department' && $recordType -> value != 'unit') {
            $recordType -> valid = 0;
            $recordType -> err_msg = "Invalid Record Type";
        }
    }

    if ($referenceRecordId -> valid == 1 && $recordType -> valid == 1 && $employeeId -> valid == 1) {
        // Validate Reference Record ID
        $referenceRecord = '';
        if ($recordType -> value == 'division') {
            $referenceRecord = MscDivision::show($referenceRecordId -> value);
        } else if ($recordType -> value == 'department') {
            $referenceRecord = MscDepartment::show($referenceRecordId -> value);
        } else if ($recordType -> value == 'unit') {
            $referenceRecord = MscUnit::show($referenceRecordId -> value);
        }

        if (count($referenceRecord) == 0) {
            $referenceRecordId -> valid = 0;
            $referenceRecordId -> err_msg = "Reference Record doesn't exists";
        }
    }

    if ($referenceRecordId -> valid == 1 && $recordType -> valid == 1 && $employeeId -> valid == 1) {
        $employee = Employee::show($employeeId -> value);
        if (is_array($employee)) {
            if (!(count($employee) > 0)) {
                $employeeId -> valid = 0;
                $employeeId -> err_msg = 'Employee Record Not Found';
            }
        } else {
            $employeeId -> valid = 0;
            $employeeId -> err_msg = 'Employee Record Not Found';
        }
    }

    if ($referenceRecordId -> valid == 1 && $recordType -> valid == 1 && $employeeId -> valid == 1) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => 'Registration',
            "past" => 'Registered',
            "future" => 'Register'
        );
        $dataContainer = array(
            "FK_employee" => $employeeId -> value,
            "FK_mscRecord" => $referenceRecordId -> value,
            "recordType" => $recordType -> value
        );
        $isSuccess = MscEmailNotif::create($dataContainer);
        if ($isSuccess) {
            // die('waka');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Email Recipient ' . $modalLbl['present'] . ' Success</h2>
                    <p class="para-text">Email Recipient ' . $modalLbl['past'] . ' Successfully</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'btn-trigger',
                    "btnLbl" => 'OK',
                )
            );
        } else {
            // die('waka2');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize col-12">Error Encountered</h2>
                    <p class="para-text col-12">Error Details: Unable to ' . $modalLbl['future'] . ' Division Record</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        if ($referenceRecordId -> valid == 0 || $recordType -> valid == 0) {
            $errorMessage = '';
            if ($referenceRecordId -> valid == 0) {
                $errorMessage = $referenceRecordId -> err_msg;
            } else if ($recordType -> valid == 0) {
                $errorMessage = $recordType -> err_msg;
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
        } else {
            $employeeIdErr = new error_handler($employeeId -> err_msg);

            $employeeRecords = Employee::index();
            $employeeNoElem = "
                <select name='employeeId' class='form-control {$employeeIdErr -> error_class}'>
                    <option value=''>Select Recipient</option>
            ";
            if (is_array($employeeRecords)) {
                if (count($employeeRecords) > 0) {
                    foreach ($employeeRecords as $employeeRecord) {
                        $isExists = MscEmailNotif::filter(array(
                            "FK_mscRecord" => $referenceRecordId -> value,
                            "recordType" => $recordType -> value,
                            "FK_employee" => $employeeRecord['PK_employee']
                        ));
                        if (count($isExists) > 0) {
                            continue;
                        }
                        $employeeName = "{$employeeRecord['lastName']}, {$employeeRecord['firstName']} " . substr($employeeRecord['middleName'], 0, 1) . ".";
                        $employeeNoElem .= "<option value='{$employeeRecord['PK_employee']}'>{$employeeName}</option>";
                    }
                }
            }
            $employeeNoElem .= "</select>";

            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Notification Recipient Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="division-form" action="../core/ajax/recipient-manage.php" tran-type="async-form">
                            <input type="text" name="referenceRecordId" hidden="hidden" value="' . $referenceRecordId -> value . '">
                            <input type="text" name="recordType" hidden="hidden" value="' . $recordType -> value . '">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Employee Name: </label>
                                        <div class="form-group col-sm-12">
                                            ' . $employeeNoElem . '
                                            ' . $employeeIdErr -> error_icon . '
                                            ' . $employeeIdErr -> error_text . '
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