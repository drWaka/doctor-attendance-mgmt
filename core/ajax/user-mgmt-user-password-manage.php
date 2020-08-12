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

// die(var_dump(isset($_POST['currentPassword'])) . '<br>');
// die(var_dump(isset($_POST['newPassword'])) . '<br>');
// die(var_dump(isset($_POST['reNewPassword'])) . '<br>');
// die(var_dump(isset($_POST['userId'])) . '<br>');
// die(var_dump(isset($_POST['tranType'])) . '<br>');

if (
    isset($_POST['currentPassword']) && 
    isset($_POST['newPassword']) && 
    isset($_POST['reNewPassword']) && 
    isset($_POST['userId']) && 
    isset($_POST['tranType'])
) {
    $userId = new form_validation($_POST['userId'], 'int', 'User ID', true);
    $tranType = new form_validation($_POST['tranType'], 'str', 'Transaction Type', true);

    $isRequired = ($tranType -> value != 'adminReset') ? true : false;
    $currentPassword = new form_validation($_POST['currentPassword'], 'password', 'Current Password', $isRequired);
    $newPassword = new form_validation($_POST['newPassword'], 'password', 'New Password', true);
    $isRequired = ($newPassword -> valid == 1 && !empty($newPassword -> value)) ? true : false;
    $reNewPassword = new form_validation($_POST['reNewPassword'], 'str-int', 'New Password', $isRequired);
    
    if (
        $userId -> valid == 1 && $tranType -> valid == 1 && $currentPassword -> valid == 1 &&
        $newPassword -> valid == 1 && $reNewPassword -> valid == 1
    ) {
        // Validate User ID Uniqueness
        $userRecord = UserMstr::show($userId -> valid);
        if (!(count($userRecord) > 0)) {
            $userId -> valid = 0;
            $userId -> err_msg = 'User record not found<br>Please contact your System Administrator';
        }
    }

    if (
        $userId -> valid == 1 && $tranType -> valid == 1 && $currentPassword -> valid == 1 &&
        $newPassword -> valid == 1 && $reNewPassword -> valid == 1
    ) {
        // Validate Transaction Type
        if ($tranType -> value !== 'adminReset' && $tranType -> value !== 'userReset') {
            $tranType -> valid = 0;
            $tranType -> err_msg = 'Transaction Type is Invalid';
        }
    }

    if (
        $userId -> valid == 1 && $tranType -> valid == 1 && $currentPassword -> valid == 1 &&
        $newPassword -> valid == 1 && $reNewPassword -> valid == 1
    ) {
        if ($tranType -> value == 'userReset') {
            // Validate if current password is correct
            $userRec = UserAccount::getUserMasterlist(array("userId" => $userId -> value));
            $userRec = UserAccount::getByLogin(array("userId" => $userRec[0]['user_id'], "password" => $currentPassword -> value));
            if (count($userRec) == 0) {
                $currentPassword -> valid = 0;
                $currentPassword -> err_msg = 'Password is incorrect';
            }
        }
    }

    if (
        $userId -> valid == 1 && $tranType -> valid == 1 && $currentPassword -> valid == 1 &&
        $newPassword -> valid == 1 && $reNewPassword -> valid == 1
    ) {
        // Validate if New Passwords matches
        if ($newPassword -> value != $reNewPassword -> value) {
            $newPassword -> valid = 0;
            $newPassword -> err_msg = 'flag';
            $reNewPassword -> valid = 0;
            $reNewPassword -> err_msg = 'Password do not match';
        }
    }

    if (
        $userId -> valid == 1 && $tranType -> valid == 1 && $currentPassword -> valid == 1 &&
        $newPassword -> valid == 1 && $reNewPassword -> valid == 1
    ) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => 'Updating',
            "past" => 'Updated',
            "future" => 'Update'
        );

        $isSuccess = UserAccount::updatePassword(array(
            "password" => $newPassword -> value,
            "userId" => $userId -> value
        ));
        if ($isSuccess) {
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize( 
                '<div class="row text-center">
                    <div class="col-sm-12">
                    <h2 class="header capitalize">Password ' . $modalLbl['present'] . ' Success</h2>
                    <p class="para-text">Password has been successfully ' . $modalLbl['past'] . '.</p>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'btn-trigger',
                    "btnLbl" => 'Okay, Thanks!'
                )
            );
        }

        if (!$isSuccess) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: Unable to {$modalLbl['present']} Password<br> Please contact your system administrator</p>
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
            } else if ($tranType -> valid == 0) {
                $errorMessage = $tranType -> err_msg;
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
            $currentPasswordErr = new error_handler($currentPassword -> err_msg);
            $newPasswordErr = new error_handler($newPassword -> err_msg);
            $reNewPasswordErr = new error_handler($reNewPassword -> err_msg);

            $hideField = ($tranType -> value == 'adminReset') ? 'hide' : '';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                '<div class="row">
                    <div class="col-sm-12">
                    <h2 class="header capitalize text-center">Change Password</h2>
                    <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                    </div>
                    
                    <div class="col-sm-12 item-guide-mgmt">
                        <form form-name="respondent-form" action="../core/ajax/user-mgmt-user-password-manage.php" tran-type="async-form">
                            <input type="text" name="userId" hidden="hidden" value="' . $userId -> value . '">
                            <input type="text" name="tranType" hidden="hidden" value="' . $tranType -> value . '">
    
                            <div class="row ' . $hideField . '">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Current Password : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="password" class="form-control ' . $currentPasswordErr -> error_class . '" name="currentPassword" placeholder="Current Password">
                                            ' . $currentPasswordErr -> error_icon . '
                                            ' . $currentPasswordErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">New Password : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="password" class="form-control ' . $newPasswordErr -> error_class . '" name="newPassword" placeholder="New Password">
                                            ' . $newPasswordErr -> error_icon . '
                                            ' . $newPasswordErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="" class="text-left control-label col-sm-12">Re-enter New Password : </label>
                                        <div class="form-group col-sm-12">
                                            <input type="password" class="form-control ' . $reNewPasswordErr -> error_class . '" name="reNewPassword" placeholder="Re-enter New Password">
                                            ' . $reNewPasswordErr -> error_icon . '
                                            ' . $reNewPasswordErr -> error_text . '
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                        </form>
                    </div>
                </div>', 
                array(
                    "trasnType" => 'regular',
                    "btnLbl" => 'Change'
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