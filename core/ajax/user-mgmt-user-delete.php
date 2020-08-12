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

if (isset($_POST['recordId'])) {
    $recordId = new form_validation($_POST['recordId'], 'int', 'User System ID', true);
    
    if ($recordId -> valid == 1) {
        // Validate User ID Uniqueness
        $userRecord = UserMstr::show($recordId -> value);
        if (count($userRecord) == 0) {
            $recordId -> valid = 0;
            $recordId -> err_msg = 'User Record doesn\'t exists';
        }
    }

    if ($recordId -> valid == 1) {
        $isSuccess = true;
        $modalLbl = array(
            "present" => 'Delete',
            "past" => 'Deleted',
            "future" => 'Delete'
        );

        $userAccount = UserAccount::getUserMasterlist(array("userId" => $recordId -> value));
        $isSuccess = UserAccount::delete($userAccount[0]['PK_userAcc']);
        if ($isSuccess) {
            $isSuccess = UserMstr::delete($recordId -> valid);
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
        $errorMessage = '';
        if ($recordId -> valid == 0) {
            $errorMessage = $recordId -> err_msg;
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