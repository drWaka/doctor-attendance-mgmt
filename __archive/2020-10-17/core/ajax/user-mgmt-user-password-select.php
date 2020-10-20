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

if (isset($_POST['userMstrId']) && isset($_POST['tranType'])) {
    $userMstrId = new form_validation($_POST['userMstrId'], 'str-int', 'User ID', true);
    $tranType = new form_validation($_POST['tranType'], 'str', 'Transaction Type', true);
    
    $userRecord = '';
    if ($userMstrId -> valid == 1 && $tranType -> valid == 1) {
        // Validate User ID
        $userRecord = UserAccount::getUserMasterlist(array(
            "userId" => $userMstrId -> value
        ));
        if (!(count($userRecord) > 0) && $userMstrId -> value != 'new') {
            $userMstrId -> valid = 0;
            $userMstrId -> err_msg = 'Invalid User ID';
        } else {
            if ($userMstrId -> value == 'new') {
                $userRecord = array(
                    "PK_userMstr" => '',
                    "lname" => '',
                    "fname" => '',
                    "email" => '',
                    "user_id" => '',
                    "PK_userType" => '',
                    "userType" => '',
                    "accountStatus" => '',
                    "isActive" => 1
                );
            } else {
                $userRecord = $userRecord[0];
            }
        }
    }

    if ($userMstrId -> valid == 1 && $tranType -> valid == 1) {
        // Validate Transaction Type
        if ($tranType -> value != 'adminReset' && $tranType -> value != 'userReset') {
            $tranType -> valid = 0;
            $tranType -> err_msg = 'Transaction Type is invalid <br>Please coordinate with your System Administrator';
        }
    }

    if ($userMstrId -> valid == 1 && $tranType -> valid == 1) {        
        $response['contentType'] = 'modal';

        $hideField = ($tranType -> value == 'adminReset') ? 'hide' : '';
        $response['content']['modal'] = modalize(
            '<div class="row">
                <div class="col-sm-12">
                <h2 class="header capitalize text-center">Change Password</h2>
                <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                </div>
                
                <div class="col-sm-12 item-guide-mgmt">
                    <form form-name="respondent-form" action="../core/ajax/user-mgmt-user-password-manage.php" tran-type="async-form">
                        <input type="text" name="userId" hidden="hidden" value="' . $userMstrId -> value . '">
                        <input type="text" name="tranType" hidden="hidden" value="' . $tranType -> value . '">

                        <div class="row ' . $hideField . '">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Current Password : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="password" class="form-control" name="currentPassword" placeholder="Current Password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">New Password : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="password" class="form-control" name="newPassword" placeholder="New Password">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Re-enter New Password : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="password" class="form-control" name="reNewPassword" placeholder="Re-enter New Password">
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
    } else {
        $errorMessage = '';
        if ($userMstrId -> valid == 0) {
            $errorMessage = $userMstrId -> err_msg;
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