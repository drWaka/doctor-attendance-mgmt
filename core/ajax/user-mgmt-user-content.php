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
    isset($_POST['userName']) && 
    isset($_POST['userTypeId']) && 
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $userName = new form_validation($_POST['userName'], 'str-int', 'User Name', false);
    $userTypeId = new form_validation($_POST['userTypeId'], 'str-int', 'User Type ID', true);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if (
        $userName -> valid == 1 && $userTypeId -> valid == 1 && 
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
    ) {
        // Validate Registry Type
        $userType = UserType::show($userTypeId -> value);
        if (!(count($userType) > 0) && $userTypeId -> value != 'all') {
            $userTypeId -> valid = 0;
            $userTypeId -> err_msg = 'Invalid User Type ID';
        }
    }

    if (
        $userName -> valid == 1 && $userTypeId -> valid == 1 && 
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
    ) {
        $userTypeFields = array();
        if ($userTypeId -> value == 'all') {
            $userTypeIds = UserType::index();
            if (is_array($userTypeIds)) {
                if (count($userTypeIds) > 0) {
                    foreach ($userTypeIds as $userTypeId) {
                        $userTypeFields[count($userTypeFields)] = $userTypeId['PK_userType'];
                    }
                }
            }
        } else {
            $userTypeFields[count($userTypeFields)] = $userTypeId -> value;
        }

        $userAccounts = UserAccount::getUserMasterlist(array(
            "userName_id" => $userName -> value,
            "userTypeId" => $userTypeFields
        ));
        if (count($userAccounts)) {
            $response['content']['total'] = count($userAccounts);
            
            $startPoint = (((intval($currentPage -> value) - 1) * $pageLimit -> value));
            $endPoint = $startPoint + $pageLimit -> value;
            for ($i = $startPoint; $i <= $endPoint ; $i++) {
                if (!isset($userAccounts[$i])) {
                    continue;
                }
                $response['content']['record'] .= "
                    <tr>
                        <td>{$userAccounts[$i]['user_id']}</td>
                        <td>{$userAccounts[$i]['lname']}, {$userAccounts[$i]['fname']}</td>
                        <td>{$userAccounts[$i]['userType']}</td>
                        <td>{$userAccounts[$i]['accountStatus']}</td>
                        <td class='text-center'> 
                            <button class='btn btn-outline-info transaction-btn'
                                trans-name='async-form'
                                data-link='../core/ajax/user-mgmt-user-select.php'
                                data-content='{
                                    &quot;userMstrId&quot; : &quot;{$userAccounts[$i]['PK_userMstr']}&quot;
                                }'
                                data-target='modal-container'
                                title='Edit User'
                            ><i class='fa fa-pencil-alt'></i></button>
                            <button class='btn btn-outline-danger transaction-btn' 
                                data-link='../core/ajax/generic-warning-modal.php' 
                                data-target='modal-container' 
                                trans-name='async-form' 
                                data-content='{
                                    &quot;transType&quot;   : &quot;delete&quot;,
                                    &quot;link&quot;        : &quot;../core/ajax/user-mgmt-user-delete.php&quot;,
                                    &quot;dataContent&quot; : {
                                        &quot;recordId&quot;   : &quot;{$userAccounts[$i]['PK_userMstr']}&quot;
                                    },
                                    &quot;headerTitle&quot; : &quot;User&quot;
                                }'
                                title='Delete User'
                            ><i class='fas fa-trash'></i></button>
                            <button class='btn btn-outline-warning transaction-btn'
                                trans-name='async-form'
                                data-link='../core/ajax/user-mgmt-user-password-select.php'
                                data-content='{
                                    &quot;userMstrId&quot; : &quot;{$userAccounts[$i]['PK_userMstr']}&quot;,
                                    &quot;tranType&quot; : &quot;adminReset&quot;
                                }'
                                data-target='modal-container'
                                title='Reset Account Password'
                            ><i class='fa fa-lock'></i></button>
                        </td>
                    </tr>
                ";
            }
        }

        if (strlen($response['content']['record']) == 0) {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] = '<tr><td class="text-center" colspan="5">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($userName -> valid == 0) {
            $errorMessage = $userName -> err_msg;
        } else if ($userTypeId -> valid == 0) {
            $errorMessage = $userTypeId -> err_msg;
        } else if ($pageLimit -> valid == 0) {
            $errorMessage = $pageLimit -> err_msg;
        } else if ($currentPage -> valid == 0) {
            $errorMessage = $currentPage -> err_msg;
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