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
    isset($_POST['sunSchedStart']) && isset($_POST['sunSchedEnd']) && 
    isset($_POST['monSchedStart']) && isset($_POST['monSchedEnd']) && 
    isset($_POST['tueSchedStart']) && isset($_POST['tueSchedEnd']) && 
    isset($_POST['wedSchedStart']) && isset($_POST['wedSchedEnd']) && 
    isset($_POST['thuSchedStart']) && isset($_POST['thuSchedEnd']) && 
    isset($_POST['friSchedStart']) && isset($_POST['friSchedEnd']) && 
    isset($_POST['satSchedStart']) && isset($_POST['satSchedEnd']) && 
    isset($_POST['employeeId'])
) {
    $sunSchedStart = new form_validation($_POST['sunSchedStart'], 'str-int', 'Sunday Schedule Start', true);
    $sunSchedEnd = new form_validation($_POST['sunSchedEnd'], 'str-int', 'Sunday Schedule End', true);
    $monSchedStart = new form_validation($_POST['monSchedStart'], 'str-int', 'Monday Schedule Start', true);
    $monSchedEnd = new form_validation($_POST['monSchedEnd'], 'str-int', 'Monday Schedule End', true);
    $tueSchedStart = new form_validation($_POST['tueSchedStart'], 'str-int', 'Tuesday Schedule Start', true);
    $tueSchedEnd = new form_validation($_POST['tueSchedEnd'], 'str-int', 'Tuesday Schedule End', true);
    $wedSchedStart = new form_validation($_POST['wedSchedStart'], 'str-int', 'Wednesday Schedule Start', true);
    $wedSchedEnd = new form_validation($_POST['wedSchedEnd'], 'str-int', 'Wednesday Schdule End', true);
    $thuSchedStart = new form_validation($_POST['thuSchedStart'], 'str-int', 'Thursday Schdule Start', true);
    $thuSchedEnd = new form_validation($_POST['thuSchedEnd'], 'str-int', 'Thursday Schdule End', true);
    $friSchedStart = new form_validation($_POST['friSchedStart'], 'str-int', 'Friday Schdule Start', true);
    $friSchedEnd = new form_validation($_POST['friSchedEnd'], 'str-int', 'Friday Schdule End', true);
    $satSchedStart = new form_validation($_POST['satSchedStart'], 'str-int', 'Saturday Schdule Start', true);
    $satSchedEnd = new form_validation($_POST['satSchedEnd'], 'str-int', 'Saturday Schdule End', true);
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Employee ID', true);

    $flags = array(
        'hasError' => 0,
        'isDone' => 0
    );

    if (
        $sunSchedStart -> valid == 1 && $sunSchedEnd -> valid == 1 && 
        $monSchedStart -> valid == 1 && $monSchedEnd -> valid == 1 && 
        $tueSchedStart -> valid == 1 && $tueSchedEnd -> valid == 1 && 
        $wedSchedStart -> valid == 1 && $wedSchedEnd -> valid == 1 && 
        $thuSchedStart -> valid == 1 && $thuSchedEnd -> valid == 1 && 
        $friSchedStart -> valid == 1 && $friSchedEnd -> valid == 1 && 
        $satSchedStart -> valid == 1 && $satSchedEnd -> valid == 1 && 
        $employeeId -> valid == 1
    ) {
        // Verify if the Employee ID is valid
        $employee = Employee::show($employeeId -> value);
        if (is_null($employee)) {
            if ($employeeId -> value !== 'new-rec') {
                $employeeId -> valid = 0;
                $employeeId -> err_msg = "Employee Record not found";
            }
        }
    }

    if (
        $sunSchedStart -> valid == 1 && $sunSchedEnd -> valid == 1 && 
        $monSchedStart -> valid == 1 && $monSchedEnd -> valid == 1 && 
        $tueSchedStart -> valid == 1 && $tueSchedEnd -> valid == 1 && 
        $wedSchedStart -> valid == 1 && $wedSchedEnd -> valid == 1 && 
        $thuSchedStart -> valid == 1 && $thuSchedEnd -> valid == 1 && 
        $friSchedStart -> valid == 1 && $friSchedEnd -> valid == 1 && 
        $satSchedStart -> valid == 1 && $satSchedEnd -> valid == 1 && 
        $employeeId -> valid == 1
    ) {
        $isSuccess = true;
        $errorMessage = '';
        $modalLbl = array(
            "present" => 'Updating',
            "past" => 'Updated',
            "future" => 'Update'
        );
        $schedule = array(
            "employeeId" => $employeeId -> value,
            "schedule" => array(
                array(
                    "day" => 'SUN',
                    "start" => '1970-01-01 ' . $sunSchedStart -> value,
                    "end" => '1970-01-01 ' . $sunSchedEnd -> value
                ), 
                array(
                    "day" => 'MON',
                    "start" => '1970-01-01 ' . $monSchedStart -> value,
                    "end" => '1970-01-01 ' . $monSchedEnd -> value
                ), 
                array(
                    "day" => 'TUE',
                    "start" => '1970-01-01 ' . $tueSchedStart -> value,
                    "end" => '1970-01-01 ' . $tueSchedEnd -> value
                ), 
                array(
                    "day" => 'WED',
                    "start" => '1970-01-01 ' . $wedSchedStart -> value,
                    "end" => '1970-01-01 ' . $wedSchedEnd -> value
                ), 
                array(
                    "day" => 'THU',
                    "start" => '1970-01-01 ' . $thuSchedStart -> value,
                    "end" => '1970-01-01 ' . $thuSchedEnd -> value
                ), 
                array(
                    "day" => 'FRI',
                    "start" => '1970-01-01 ' . $friSchedStart -> value,
                    "end" => '1970-01-01 ' . $friSchedEnd -> value
                ), 
                array(
                    "day" => 'SAT',
                    "start" => '1970-01-01 ' . $satSchedStart -> value,
                    "end" => '1970-01-01 ' . $satSchedEnd -> value
                ), 
            )
        );

        foreach ($schedule['schedule'] as $sched) {
            $schedRec = EmployeeClinicSchedule::filter(array(
                'employeeId' => $schedule['schedule'],
                'day' => $sched['day']
            ));

            if (count($schedRec) > 0) {
                $updateParam = array(
                    'PK_employee_clinic_sched' => $schedRec[0]['PK_employee_clinic_sched'],
                    'time_start' => $sched['start'],
                    'time_end' => $sched['end']
                );
                if (!EmployeeClinicSchedule::update($updateParam)) {
                    $isSuccess = false;
                    $errorMessage = 'Unable to update Doctor Schedule Record #' . $updateParam['PK_employee_clinic_sched'];
                }
            } else {
                $isSuccess = false;
                $errorMessage = 'Doctor Schedule Record for "' . $sched['day'] . '" Not Found';
            }

        }
        if ($isSuccess) {
            // die('waka');
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
            // die('waka2');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize col-12">Error Encountered</h2>
                    <p class="para-text col-12">Error Details: ' . $errorMessage . ' </p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        if ($employeeId -> valid == 0) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$employeeId -> err_msg}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        } else {
            $employeeNoErr = new error_handler($employeeNo -> err_msg);
            $departmentIdErr = new error_handler($departmentId -> err_msg);
            $firstNameErr = new error_handler($firstName -> err_msg);
            $middleNameErr = new error_handler($middleName -> err_msg);
            $lastNameErr = new error_handler($lastName -> err_msg);
            $birthDateErr = new error_handler($birthDate -> err_msg);
            $genderErr = new error_handler($gender -> err_msg);
            $mobileNoErr = new error_handler($mobileNo -> err_msg);
            $addressLine1Err = new error_handler($addressLine1 -> err_msg);
            $addressLine2Err = new error_handler($addressLine2 -> err_msg);
            $addressLine3Err = new error_handler($addressLine3 -> err_msg);
            $emailErr = new error_handler($email -> err_msg);
            
            $departmentElem = "";
            $departments = MscDepartment::index();
            $departmentElem .= "<select name='departmentId' class='form-control {$departmentIdErr -> error_class}'>";
            $departmentElem .= "<option value='' style='display:none;'>Choose a Department</option>";
            if (!is_null($departments)) {
                if (count($departments) > 0) {
                    foreach ($departments as $department) {
                        $selected = ($department['PK_mscdepartment'] == $departmentId -> value) ? "selected" : "";
                        $departmentElem .= "<option value='{$department['PK_mscdepartment']}' $selected>{$department['description']}</option>";
                    }
                }
            }
            $departmentElem .= "</select>";
            
            $maleSelected = (strtolower($gender -> value) == 'm') ? "selected" : "";
            $femaleSelected = (strtolower($gender -> value) == 'f') ? "selected" : "";
            $genderElem = "<select name='gender' class='form-control {$genderErr -> error_class}'>
                <option value='' style='display:none;'>Choose a Gender</option>
                <option value='M' {$maleSelected}>Male</option>
                <option value='F' {$femaleSelected}>Female</option>
            </select>";

            $employeeBirthdate = "";
            if (!empty($birthDate -> value)) {
                $employeeBirthdate = date('Y-m-d', strtotime($birthDate -> value));
            }

            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Doctor Record Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="respondent-form" action="../core/ajax/doctor-manage.php" tran-type="async-form">
                            <input type="text" name="employeeId" hidden="hidden" value="' . $employeeId -> value . '">
    
                            <div class="row">
                                <div class="col-12"><b>Doctor Information</b></div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">PRC No. : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $employeeNoErr -> error_class . '" name="employeeNo" placeholder="PRC No" value="' . $employeeNo -> value . '">
                                            ' . $employeeNoErr -> error_icon . '
                                            ' . $employeeNoErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Department : </label>
                                        <div class="form-group col-sm-12">
                                            ' . $departmentElem . '
                                            ' . $departmentIdErr -> error_icon . '
                                            ' . $departmentIdErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row margin-top-xs">
                                <div class="col-12"><b>Personal Information</b></div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">First Name : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $firstNameErr -> error_class . '" name="firstName" placeholder="First Name" value="' . ($firstName -> value) . '">
                                            ' . $firstNameErr -> error_icon . '
                                            ' . $firstNameErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Middle Name : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $middleNameErr -> error_class . '" name="middleName" placeholder="Middle Name" value="' . ($middleName -> value) . '">
                                            ' . $middleNameErr -> error_icon . '
                                            ' . $middleNameErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Last Name : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $lastNameErr -> error_class . '" name="lastName" placeholder="Last Name" value="' . ($lastName -> value) . '">
                                            ' . $lastNameErr -> error_icon . '
                                            ' . $lastNameErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Gender : </label>
                                        <div class="form-group col-sm-12">
                                            ' . $genderElem . '
                                            ' . $genderErr -> error_icon . '
                                            ' . $genderErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Birthdate : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="date" class="form-control uppercase ' . $birthDateErr -> error_class . '" name="birthDate" value="' . $employeeBirthdate . '">
                                            ' . $birthDateErr -> error_icon . '
                                            ' . $birthDateErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row margin-top-xs">
                                <div class="col-12"><b>Contact Information</b></div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Mobile No. : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $mobileNoErr -> error_class . '" name="mobileNo" placeholder="Mobile No." value="' . $mobileNo -> value . '">
                                            ' . $mobileNoErr -> error_icon . '
                                            ' . $mobileNoErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Email : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $emailErr -> error_class . '" name="email" placeholder="Email" value="' . $email -> value . '">
                                            ' . $emailErr -> error_icon . '
                                            ' . $emailErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Street, Zone, Barangay : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $addressLine1Err -> error_class . '" name="addressLine1" placeholder="Street, Zone, Barangay" value="' . $addressLine1 -> value . '">
                                            ' . $addressLine1Err -> error_icon . '
                                            ' . $addressLine1Err -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">City : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $addressLine2Err -> error_class . '" name="addressLine2" placeholder="City" value="' . $addressLine2 -> value . '">
                                            ' . $addressLine2Err -> error_icon . '
                                            ' . $addressLine2Err -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Province : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $addressLine3Err -> error_class . '" name="addressLine3" placeholder="Province" value="' . $addressLine3 -> value . '">
                                            ' . $addressLine3Err -> error_icon . '
                                            ' . $addressLine3Err -> error_text . '
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
                ),
                'modal-lg'
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