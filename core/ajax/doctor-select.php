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

if (isset($_POST['employeeId'])) {
    $employeeId = new form_validation($_POST['employeeId'], 'str-int', 'Employee ID', true);

    $employee = '';
    if ($employeeId -> valid == 1) {
        // Verify if the Employee ID is valid
        if ($employeeId -> value == 'new-rec') {
            $employee = array(
                "firstName" => '',
                "middleName" => '',
                "lastName" => '',
                "birthDate" => '',
                "gender" => '',
                "mobileNo" => '',
                "AddressLine1" => '',
                "AddressLine2" => '',
                "AddressLine3" => '',
                "employeeNo" => '',
                "email" => '',
                "clinic" => '',
                "FK_mscDepartment" => 0,
                "fingerScanId" => ''
            );
        } else {
            $employee = Employee::show($employeeId -> value);
            // die(var_dump($employee));
            if (is_null($employee)) {
                $employeeId -> valid = 0;
                $employeeId -> err_msg = "Employee Record not found";
            }
        }
    }

    if ($employeeId -> valid == 1) {
        $departmentElem = "";
        $departments = MscDepartment::index();
        $departmentElem .= "<select name='departmentId' class='form-control'>";
        $departmentElem .= "<option value='' style='display:none;'>Choose a Department / Specialization</option>";
        if (!is_null($departments)) {
            if (count($departments) > 0) {
                foreach ($departments as $department) {
                    $selected = ($department['PK_mscdepartment'] == $employee['FK_mscDepartment']) ? "selected" : "";
                    $departmentElem .= "<option value='{$department['PK_mscdepartment']}' $selected>{$department['description']} / {$department['specialization']}</option>";
                }
            }
        }
        $departmentElem .= "</select>";
        
        $maleSelected = (strtolower($employee['gender']) == 'm') ? "selected" : "";
        $femaleSelected = (strtolower($employee['gender']) == 'f') ? "selected" : "";
        $genderElem = "<select name='gender' class='form-control'>
            <option value='' style='display:none;'>Choose a Gender</option>
            <option value='M' {$maleSelected}>Male</option>
            <option value='F' {$femaleSelected}>Female</option>
        </select>";

        $employeeBirthdate = "";
        if (!empty($employee['birthDate'])) {
            $employeeBirthdate = date('Y-m-d', strtotime($employee['birthDate']));
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
                                        <input type="text" class="form-control" name="employeeNo" placeholder="PRC No" value="' . $employee['employeeNo'] . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Department / Specialization : </label>
                                    <div class="form-group col-sm-12">
                                        ' . $departmentElem . '
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Clinic Room No. : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="clinic" placeholder="Clinic Room No." value="' . $employee['clinic'] . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Finger Scan ID : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="fingerScanId" placeholder="000000000" value="' . $employee['fingerScanId'] . '">
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
                                        <input type="text" class="form-control" name="firstName" placeholder="First Name" value="' . utf8_encode($employee['firstName']) . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Middle Name : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="middleName" placeholder="Middle Name" value="' . utf8_encode($employee['middleName']) . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Last Name : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="lastName" placeholder="Last Name" value="' . utf8_encode($employee['lastName']) . '">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Gender : </label>
                                    <div class="form-group col-sm-12">
                                        ' . $genderElem . '
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Birthdate : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="date" class="form-control uppercase" name="birthDate" value="' . $employeeBirthdate . '">
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
                                        <input type="text" class="form-control" name="mobileNo" placeholder="Mobile No." value="' . $employee['mobileNo'] . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Email : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="email" placeholder="Email" value="' . utf8_encode($employee['email']) . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Street, Zone, Barangay : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="addressLine1" placeholder="Street, Zone, Barangay" value="' . utf8_encode($employee['AddressLine1']) . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">City : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="addressLine2" placeholder="City" value="' . utf8_encode($employee['AddressLine2']) . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Province : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="addressLine3" placeholder="Province" value="' . utf8_encode($employee['AddressLine3']) . '">
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
    } else {
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