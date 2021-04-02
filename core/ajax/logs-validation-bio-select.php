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

if (isset($_POST['recordId'])) {
    $recordId = new form_validation($_POST['recordId'], 'int', 'Logs Validation Document ID', true);

    $voidedAttendanceRecord = '';
    if ($recordId -> valid == 1) {
        $voidedAttendanceRecord = EmployeeAttendanceVoid::show($recordId -> value);
        // die(var_dump($voidedAttendanceRecord));
        if (is_null($voidedAttendanceRecord)) {
            $recordId -> valid = 0;
            $recordId -> err_msg = "Logs Validation Document doesn't exists";
        }
    }

    if ($recordId -> valid == 1) {
        $employeeAttendanceVoid = EmployeeAttendanceVoid::show($recordId -> value);
        $employeeAttendanceVoidContent = EmployeeAttendanceVoidContent::filter(array(
            "logValidationId" => $recordId -> value
        ));

        $employee = Employee::show($employeeAttendanceVoid['FK_employee']);
        if (
            $employeeAttendanceVoid['isPosted'] != 1 && 
            $employeeAttendanceVoid['isCancelled'] != 1 && 
            $employeeAttendanceVoid['isVoided'] != 1 
        ) {
            // Document Content Management
            $excludedLogs = [];
            $currentDate = date('Y-m-d');
            $exludedLogsQry = "
                SELECT b.FK_biometric_log_id
                FROM employee_attendance_void AS a
                INNER JOIN employee_attendance_void_content AS b 
                    ON a.PK_employee_attendance_void = b.FK_employee_attendance_void
                WHERE a.FK_employee = '{$employeeAttendanceVoid['FK_employee']}'
                    AND date_format(a.createDate, \"%Y-%m-%d\") = '{$currentDate}'
                    AND a.isPosted = 1
                    AND (a.isCancelled != 1 OR a.isCancelled IS NULL)
                    AND (a.isVoided != 1 OR a.isVoided IS NULL)
            ";
            $exludedLogsRes = $connection->query($exludedLogsQry);
            if ($exludedLogsRes -> num_rows > 0) {
                while ($exludedLogsRow = $exludedLogsRes -> fetch_assoc()) {
                    $excludedLogs[count($excludedLogs)] = $exludedLogsRow['FK_biometric_log_id'];
                }
            }
            $employeeLogs = Bio_EmployeeLogs::filter(array(
                "dtrDate" => date('Y-m-d'),
                "fingerScanId" => $employee['fingerScanId'],
                "excludedLogs" => $excludedLogs
            ));

            $employeeLogsContent = $bioMetricLogs = '';
            foreach ($employeeLogs AS $employeeLog) {
                // Determine if the Log is already included at the document
                $includedLogs = array_filter($employeeAttendanceVoidContent, function($param){
                    if ($param['FK_biometric_log_id'] == $GLOBALS['employeeLog']['IndexKey']) return true;
                    return false;
                });
                $isSelected = (count($includedLogs) > 0) ? 'checked' : '';
                foreach ($includedLogs as $logs) { 
                    $bioMetricLogs .= (strlen($bioMetricLogs) > 0) 
                        ? ',' . $logs['FK_biometric_log_id'] 
                        : $logs['FK_biometric_log_id'];
                }

                // Log HTML Content
                $logTime = date('M d, Y H:i:s A', strtotime($employeeLog['TransactionTime']));
                $logType = ($employeeLog['FunctionKey'] == '2') ? 'Logout' : 'Login';
                $employeeLogsContent .= "
                    <tr>
                        <td><input type='checkbox' name='timeLog[]' {$isSelected} value='{$employeeLog['IndexKey']}' class='form-control' style='width: 15px;'></td>
                        <td>{$logTime}</td>
                        <td>{$logType}</td>
                    </tr>
                ";
            }

            
            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Document Content Management</h2>
                    <p class="para-text text-center">Please select the biometric logs that needed to be marked as null &amp; void.</p>
                    </div>
                    
                    <div class="col-sm-12 logs-validation-form">
                        <form form-name="respondent-form" action="../core/ajax/logs-validation-bio-manage.php" tran-type="async-form">
                            <input type="text" name="recordId" hidden="hidden" value="' . $recordId -> value . '">
                            <input type="text" name="bioLogs" hidden="hidden" value="' . $bioMetricLogs . '">

                            <div class="row">
                                <div class="col-12">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Log Time</th>
                                            <th>Log Type</th>
                                        </tr>
                                    </thead>
                                    ' . $employeeLogsContent . '
                                </table>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'regular',
                    "btnLbl" => 'Submit'
                ),
                'modal-lg'
            );
        } else {
            // Viewing Document Content
            $includedLogs = array();
            foreach ($employeeAttendanceVoidContent as $logs) {
                $includedLogs[count($includedLogs)] = $logs['FK_biometric_log_id'];
            }

            $employeeLogs = Bio_EmployeeLogs::filter(array(
                "dtrDate" => date('Y-m-d'),
                "fingerScanId" => $employee['fingerScanId'],
                "includedLogs" => $includedLogs
            ));

            $employeeLogsContent = '';
            foreach ($employeeLogs AS $employeeLog) {
                // Log HTML Content
                $logTime = date('M d, Y H:i:s A', strtotime($employeeLog['TransactionTime']));
                $logType = ($employeeLog['FunctionKey'] == '2') ? 'Logout' : 'Login';
                $employeeLogsContent .= "
                    <tr>
                        <td><input type='checkbox' name='timeLog[]' checked value='{$employeeLog['IndexKey']}' class='form-control' style='width: 15px;' disabled></td>
                        <td>{$logTime}</td>
                        <td>{$logType}</td>
                    </tr>
                ";
            }

            
            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                        <h2 class="header capitalize text-center">Document Content Management</h2>
                        <p class="para-text text-center">Logs Validation Document Content</p>
                    </div>
                    
                    <div class="col-sm-12">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Log Time</th>
                                    <th>Log Type</th>
                                </tr>
                            </thead>
                            ' . $employeeLogsContent . '
                        </table>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Close'
                ),
                'modal-lg'
            );
        }
    } else {
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