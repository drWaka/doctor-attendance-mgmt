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
    isset($_POST['employeeId']) && isset($_POST['employeeNo']) && 
    isset($_POST['divisionId']) && isset($_POST['departmentId']) && 
    isset($_POST['firstName']) && isset($_POST['middleName']) && 
    isset($_POST['lastName']) && isset($_POST['birthDate']) && 
    isset($_POST['gender']) && isset($_POST['mobileNo']) && 
    isset($_POST['addressLine1']) && isset($_POST['addressLine2']) && 
    isset($_POST['addressLine3']) && isset($_POST['email']) && 
    isset($_POST['unitId'])
) {
    $employeeId = new form_validation($_POST['employeeId'], 'str-int', 'Employee ID', true);
    $employeeNo = new form_validation($_POST['employeeNo'], 'str-int', 'Employee No', true);
    $divisionId = new form_validation($_POST['divisionId'], 'int', 'Division ID', true);
    $departmentId = new form_validation($_POST['departmentId'], 'int', 'Division ID', true);
    $unitId = new form_validation($_POST['unitId'], 'int', 'Unit ID', true);
    $firstName = new form_validation($_POST['firstName'], 'str', 'First Name', true);
    $middleName = new form_validation($_POST['middleName'], 'str', 'Middle Name', false);
    $lastName = new form_validation($_POST['lastName'], 'str', 'Last Name', true);
    $birthDate = new form_validation($_POST['birthDate'], 'date', 'Birthdate', true);
    $gender = new form_validation($_POST['gender'], 'str', 'Gender', false);
    $mobileNo = new form_validation($_POST['mobileNo'], 'str-int', 'Mobile No.', false);
    $addressLine1 = new form_validation($_POST['addressLine1'], 'str-int', 'Address Line 1', false);
    $addressLine2 = new form_validation($_POST['addressLine2'], 'str-int', 'City', false);
    $addressLine3 = new form_validation($_POST['addressLine3'], 'str-int', 'Province', false);
    $email = new form_validation($_POST['email'], 'str-int', 'Email', false);

    $flags = array(
        'hasError' => 0,
        'isDone' => 0
    );

    if (
        $employeeId -> valid == 1 && $employeeNo -> valid == 1 && 
        $divisionId -> valid == 1 && $departmentId -> valid == 1 && 
        $firstName -> valid == 1 && $middleName -> valid == 1 && 
        $lastName -> valid == 1 && $birthDate -> valid == 1 && 
        $gender -> valid == 1 && $mobileNo -> valid == 1 && 
        $addressLine1 -> valid == 1 && $addressLine2 -> valid == 1 && 
        $addressLine3 -> valid == 1 && $email -> valid == 1 && 
        $unitId -> valid == 1
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
        $employeeId -> valid == 1 && $employeeNo -> valid == 1 && 
        $divisionId -> valid == 1 && $departmentId -> valid == 1 && 
        $firstName -> valid == 1 && $middleName -> valid == 1 && 
        $lastName -> valid == 1 && $birthDate -> valid == 1 && 
        $gender -> valid == 1 && $mobileNo -> valid == 1 && 
        $addressLine1 -> valid == 1 && $addressLine2 -> valid == 1 && 
        $addressLine3 -> valid == 1 && $email -> valid == 1 && 
        $unitId -> valid == 1
    ) {
        // Validate the uniqueness of Employee No
        $employee = Employee::getByEmployeeNo($employeeNo -> value);
        if (!is_null($employee)) {
            if (count($employee) > 0) {
                if ($employee[0]['PK_employee'] !== $employeeId -> value) {
                    $employeeNo -> valid = 0;
                    $employeeNo -> err_msg = "Employee No. is already taken";
                }
            }
        }
    }

    if (
        $employeeId -> valid == 1 && $employeeNo -> valid == 1 && 
        $divisionId -> valid == 1 && $departmentId -> valid == 1 && 
        $firstName -> valid == 1 && $middleName -> valid == 1 && 
        $lastName -> valid == 1 && $birthDate -> valid == 1 && 
        $gender -> valid == 1 && $mobileNo -> valid == 1 && 
        $addressLine1 -> valid == 1 && $addressLine2 -> valid == 1 && 
        $addressLine3 -> valid == 1 && $email -> valid == 1 && 
        $unitId -> valid == 1
    ) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => '',
            "past" => '',
            "future" => ''
        );
        $dataContainer = array(
            "PK_employee" => $employeeId -> value,
            "employeeNo" => $employeeNo -> value,
            "FK_mscDepartment" => $departmentId -> value,
            "FK_mscDivision" => $divisionId -> value,
            "FK_mscUnit" => $unitId -> value,
            "firstName" => $firstName -> value,
            "middleName" => $middleName -> value,
            "lastName" => $lastName -> value,
            "birthDate" => $birthDate -> value,
            "gender" => $gender -> value,
            "mobileNo" => $mobileNo -> value,
            "addressLine1" => $addressLine1 -> value,
            "addressLine2" => $addressLine2 -> value,
            "addressLine3" => $addressLine3 -> value,
            "email" => $email -> value
        );
        if ($employeeId -> value == 'new-rec') {
            $modalLbl = array(
            "present" => 'Registration',
            "past" => 'Registered',
            "future" => 'Register'
            );
            $isSuccess = Employee::insert($dataContainer);
        } else {
            $modalLbl = array(
            "present" => 'Updating',
            "past" => 'Updated',
            "future" => 'Update'
            );
            $isSuccess = Employee::update($dataContainer);
        }
        if ($isSuccess) {
            // die('waka');
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Respondent ' . $modalLbl['present'] . ' Success</h2>
                    <p class="para-text">Respondent ' . $modalLbl['past'] . ' Successfully</p>
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
                    <p class="para-text col-12">Error Details: Unable to ' . $modalLbl['future'] . ' Respondent Record</p>
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
            $divisionIdErr = new error_handler($divisionId -> err_msg);
            $departmentIdErr = new error_handler($departmentId -> err_msg);
            $unitIdErr = new error_handler($unitId -> err_msg);
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

            $divisionElem = "";
            $divisions = MscDivision::index();
            if (count($divisions) > 0) {
                $divisionElem .= "<select name='divisionId' class='form-control {$divisionIdErr -> error_class}'>";
                $divisionElem .= "<option value='' style='display:none;'>Choose a Division</option>";
                foreach ($divisions as $division) {
                    $selected = ($division['PK_mscdivision'] == $divisionId -> value) ? "selected" : "";
                    $divisionElem .= "<option value='{$division['PK_mscdivision']}' $selected>{$division['description']}</option>";
                }
                $divisionElem .= "</select>";
            }

            $departmentElem = "";
            $departments = MscDepartment::getByDivision($divisionId -> value);
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

            $unitElem = "";
            $units = MscUnit::getByDepartment($unitId -> value);
            $unitElem .= "<select name='unitId' class='form-control {$unitIdErr -> error_class}'>";

            $selected = ($unitId -> value == 0 && $employeeId -> value !== 'new-rec') 
                ? "selected"
                : "";
            $unitElem .= "
                <option value='' style='display:none;'>Choose a Unit</option>
                <option value='0' {$selected}>No Unit</option>
            ";
            if (!is_null($units)) {
                if (count($units) > 0) {
                    foreach ($units as $unit) {
                        $selected = ($unit['PK_mscUnit'] == $unitId -> value) ? "selected" : "";
                        $unitElem .= "<option value='{$unit['PK_mscUnit']}' $selected>{$unit['description']}</option>";
                    }
                }
            }
            $unitElem .= "</select>";
            
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
                    <h2 class="header capitalize text-center">Respondent Record Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="respondent-form" action="../core/ajax/respondent-manage.php" tran-type="async-form">
                            <input type="text" name="employeeId" hidden="hidden" value="' . $employeeId -> value . '">
    
                            <div class="row">
                                <div class="col-12"><b>Employee Information</b></div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Employee No. : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $employeeNoErr -> error_class . '" name="employeeNo" placeholder="Employee No" value="' . $employeeNo -> value . '">
                                            ' . $employeeNoErr -> error_icon . '
                                            ' . $employeeNoErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Division : </label>
                                        <div class="form-group col-sm-12">
                                            ' . $divisionElem . '
                                            ' . $divisionIdErr -> error_icon . '
                                            ' . $divisionIdErr -> error_text . '
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
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Unit : </label>
                                        <div class="form-group col-sm-12">
                                            ' . $unitElem . '
                                            ' . $unitIdErr -> error_icon . '
                                            ' . $unitIdErr -> error_text . '
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