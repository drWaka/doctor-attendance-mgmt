<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => ''
    ),
    "contentType" => ''
);


if (isset($_POST['userId']) && isset($_POST['password'])) {
    $userId = new form_validation($_POST['userId'], 'str-int', 'User ID', true);
    $password = new form_validation($_POST['password'], 'str-int', 'Password', true);

    if ($userId -> valid == 1 && $password -> valid == 1) {
        // Validate if the User ID is valid
        $userDetails = UserAccount::getByLogin(array(
            "userId" => $userId -> value
        ));

        if (!(count($userDetails) > 0)) {
            $userId -> valid = 0;
            $userId -> err_msg = 'User ID is Invalid';
        }
    }

    if ($userId -> valid == 1 && $password -> valid == 1) {
        // Validate if the User ID & Password are valid
        $userDetails = UserAccount::getByLogin(array(
            "userId" => $userId -> value,
            "password" => $password -> value
        ));

        if (!(count($userDetails) > 0)) {
            $password -> valid = 0;
            $password -> err_msg = 'Incorect Password';
        }
    }



    if ($userId -> valid == 1 && $password -> valid == 1) {
        $userAccount = UserAccount::getByLogin(array(
            "userId" => $userId -> value
        ));
        $userRecord = UserMstr::show($userAccount[0]['FK_userMstr']);
        $_SESSION['userId'] = $userRecord[0]['PK_userMstr'];
        $_SESSION['firstName'] = $userRecord[0]['fname'];
        $_SESSION['lastName'] = $userRecord[0]['lname'];

        header("Location: ../../admin/homepage.php");
    } else {
        if ($userId -> valid == 0) {
            $_SESSION['UserIdErr'] = $userId -> err_msg;
        }
        $_SESSION['UserIdValue'] = $userId -> value;


        if ($password -> valid == 0) {
            $_SESSION['PasswordErr'] = $password -> err_msg;
        }

        header("Location: ../../admin/index.php");
    }

} else {
    die('Unexpected Error Encountered: Please contact your system administrator');
}


// Encode JSON Response
encode_json_file($response);