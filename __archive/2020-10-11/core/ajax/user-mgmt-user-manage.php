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
    isset($_POST['userId']) && 
    isset($_POST['userTypeId']) &&
    isset($_POST['username']) &&
    isset($_POST['firstName']) &&
    isset($_POST['lastName']) &&
    isset($_POST['email']) &&
    isset($_POST['accountStatus'])
) {
    $userId = new form_validation($_POST['userId'], 'str-int', 'User System ID', true);
    $userTypeId = new form_validation($_POST['userTypeId'], 'int', 'User Type ID', true);
    $username = new form_validation($_POST['username'], 'str-int', 'User ID', true);
    $firstName = new form_validation($_POST['firstName'], 'str', 'First Name', true);
    $lastName = new form_validation($_POST['lastName'], 'str', 'Last Name', true);
    $email = new form_validation($_POST['email'], 'email', 'Email', true);
    $accountStatus = new form_validation($_POST['accountStatus'], 'int', 'Account Status', true);
    
    if (
        $userId -> valid == 1 && $userTypeId -> valid == 1 && $username -> valid == 1 &&
        $firstName -> valid == 1 && $lastName -> valid == 1 && $email -> valid == 1 && 
        $accountStatus -> valid == 1
    ) {
        // Validate User ID Uniqueness
        $userRecord = UserAccount::getByLogin(array("userId" => $username -> value), 0);
        if (count($userRecord)) {
            if ($userRecord[0]['FK_userMstr'] != $userId -> value) {
                $username -> valid = 0;
                $username -> err_msg = 'User ID already taken';
            }
        }
    }

    if (
        $userId -> valid == 1 && $userTypeId -> valid == 1 && $username -> valid == 1 &&
        $firstName -> valid == 1 && $lastName -> valid == 1 && $email -> valid == 1 && 
        $accountStatus -> valid == 1
    ) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => '',
            "past" => '',
            "future" => ''
        );
        $userMstrData = array(
            "firstName" => $firstName -> value,
            "lastName" => $lastName -> value,
            "email" => $email -> value,
            "PK_userMstr" => $userId -> value
        );

        if ($userId -> value == 'new') {
            $modalLbl = array(
                "present" => 'Registration',
                "past" => 'Registered',
                "future" => 'Register'
            );
            $userSystemId = UserMstr::create($userMstrData);
    
            if ($userSystemId) {
                $isSuccess = UserAccount::create(array(
                    "userId" => $userSystemId,
                    "userName" => $username -> value,
                    "userTypeId" => $userTypeId -> value, 
                    "isActive" => $accountStatus -> value
                ));
    
                if ($isSuccess) {
                    $response['contentType'] = 'modal';
                    $response['content']['modal'] = modalize( 
                        '<div class="row text-center">
                            <div class="col-sm-12">
                            <h2 class="header capitalize">User ' . $modalLbl['present'] . ' Success</h2>
                            <p class="para-text">User has been successfully ' . $modalLbl['past'] . '.</p>
                            </div>
                        </div>', 
                        array(
                            "trasnType" => 'btn-trigger',
                            "btnLbl" => 'Okay, Thanks!'
                        )
                    );
                }
            }
        } else {
            $modalLbl = array(
                "present" => 'Updating',
                "past" => 'Updated',
                "future" => 'Update'
            );
            $isSuccess = UserMstr::update($userMstrData);
    
            if ($isSuccess) {
                $userAccId = UserAccount::getByLogin(array("userId" => $username -> value), 0);
                $isSuccess = UserAccount::update(array(
                    "userTypeId" => $userTypeId -> value,
                    "userAccId" => $userAccId[0]['PK_userAcc'], 
                    "isActive" => $accountStatus -> value
                ));
    
                if ($isSuccess) {
                    $response['contentType'] = 'modal';
                    $response['content']['modal'] = modalize( 
                        '<div class="row text-center">
                            <div class="col-sm-12">
                            <h2 class="header capitalize">User ' . $modalLbl['present'] . ' Success</h2>
                            <p class="para-text">User has been successfully ' . $modalLbl['past'] . '.</p>
                            </div>
                        </div>', 
                        array(
                            "trasnType" => 'btn-trigger',
                            "btnLbl" => 'Okay, Thanks!'
                        )
                    );
                }
            }
        }

        

        if (!$isSuccess) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: Unable to {$modalLbl['present']} User<br> Please contact your system administrator</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
    } else {
        if ($userId -> valid == 0) {
            $errorMessage = '';
            if ($userId -> valid == 0) {
                $errorMessage = $userId -> err_msg;
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
            $usernameErr = new error_handler($username -> err_msg);
            $userTypeIdErr = new error_handler($userTypeId -> err_msg);
            $firstNameErr = new error_handler($firstName -> err_msg);
            $lastNameErr = new error_handler($lastName -> err_msg);
            $emailErr = new error_handler($email -> err_msg);
            $accountStatusErr = new error_handler($accountStatus -> err_msg);

            $userTypeElem = '';
            $userTypes = UserType::index();
            if (count($userTypes) > 0) {
                $userTypeElem .= '<select name="userTypeId" class="form-control  ' . $userTypeIdErr -> error_class . '">
                    <option value="">Select User Type</option>';
                foreach($userTypes as $userType) {
                    $selected = ($userTypeId -> value == $userType['PK_userType']) ? 'selected' : '';
                    $userTypeElem .= "<option value='{$userType['PK_userType']}' {$selected}>{$userType['description']}</option>";
                }
                $userTypeElem .= '</select>';
            }
            $isInactive = ($accountStatus -> value == 0) ? 'selected' : '';
            $accountStatusElem = "
                <select name='accountStatus' class='form-control {$accountStatusErr -> error_class}'>
                    <option value='1'>Active</option>
                    <option value='0' {$isInactive}>Inactive</option>
                </select>
            ";
            
            $disableField = ($userId -> value !== 'new') ? 'readonly' : '';
            $hideField = ($_SESSION['userType'] != 'admin' && $_SESSION['userType'] != 'administrator') ? 'hide' : '';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">User Management</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="respondent-form" action="../core/ajax/user-mgmt-user-manage.php" tran-type="async-form">
                            <input type="text" name="userId" hidden="hidden" value="' . $userId -> value . '">

                            <div class="row ' . $hideField . '">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">User ID : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $usernameErr -> error_class . '" name="username" placeholder="User ID" value="' . $username -> value . '" ' . $disableField . '>
                                            ' . $usernameErr -> error_icon . '
                                            ' . $usernameErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">User Type : </label>
                                        <div class="form-group col-sm-12">
                                            ' . $userTypeElem . '
                                            ' . $userTypeIdErr -> error_icon . '
                                            ' . $userTypeIdErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">First Name : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $firstNameErr -> error_class . '" name="firstName" placeholder="First Name" value="' . $firstName -> value . '">
                                            ' . $firstNameErr -> error_icon . '
                                            ' . $firstNameErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Last Name : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="text" class="form-control ' . $lastNameErr -> error_class . '" name="lastName" placeholder="Last Name" value="' . $lastName -> value . '">
                                            ' . $lastNameErr -> error_icon . '
                                            ' . $lastNameErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                                <div class="col-md-6 ' . $hideField . '">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Account Status : </label>
                                    <div class="form-group col-sm-12">
                                        ' . $accountStatusElem . '
                                        ' . $accountStatusErr -> error_icon . '
                                        ' . $accountStatusErr -> error_text . '
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