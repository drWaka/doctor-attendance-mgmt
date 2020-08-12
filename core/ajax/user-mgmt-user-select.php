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

if (isset($_POST['userMstrId'])) {
    $userMstrId = new form_validation($_POST['userMstrId'], 'str-int', 'User ID', true);
    
    $userRecord = '';
    if ($userMstrId -> valid == 1 ) {
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

    if ($userMstrId -> valid == 1 ) {
        $userTypeElem = '';
        $userTypes = UserType::index();
        if (count($userTypes) > 0) {
            $userTypeElem .= '<select name="userTypeId" class="form-control">
                <option value="">Select User Type</option>';
            foreach($userTypes as $userType) {
                $selected = ($userRecord['PK_userType'] == $userType['PK_userType']) ? 'selected' : '';
                $userTypeElem .= "<option value='{$userType['PK_userType']}' {$selected}>{$userType['description']}</option>";
            }
            $userTypeElem .= '</select>';
        }

        $isInactive = ($userRecord['isActive'] == 0) ? 'selected' : '';
        $accountStatusElem = "
            <select name='accountStatus' class='form-control'>
                <option value='1'>Active</option>
                <option value='0' {$isInactive}>Inactive</option>
            </select>
        ";
        
        $disableField = ($userMstrId -> value !== 'new') ? 'readonly' : '';
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
                        <input type="text" name="userId" hidden="hidden" value="' . $userMstrId -> value . '">

                        <div class="row ' . $hideField . '">
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">User ID : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="username" placeholder="User ID" value="' . $userRecord['user_id'] . '" ' . $disableField . '>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">User Type : </label>
                                    <div class="form-group col-sm-12">
                                        ' . $userTypeElem . '
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">First Name : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="firstName" placeholder="First Name" value="' . $userRecord['fname'] . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Last Name : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="lastName" placeholder="Last Name" value="' . $userRecord['lname'] . '">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Email : </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="email" placeholder="Email" value="' . $userRecord['email'] . '">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6  ' . $hideField . '">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Account Status : </label>
                                    <div class="form-group col-sm-12">
                                        ' . $accountStatusElem . '
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
        $errorMessage = '';
        if ($userMstrId -> valid == 0) {
            $errorMessage = $userMstrId -> err_msg;
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