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

if (
    isset($_POST['questionMstrId']) && isset($_POST['sessionId']) && isset($_POST['employeeId']) && 
    isset($_POST['pageNo']) && isset($_POST['groupNo'])
) {
    $questionMstrId = new form_validation($_POST['questionMstrId'], 'int', 'Question Master ID', true);
    $sessionId = new form_validation($_POST['sessionId'], 'int', 'Session ID', true);
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Employee ID', true);
    $pageNo = new form_validation($_POST['pageNo'], 'int', 'Page No', true);
    $groupNo = new form_validation($_POST['groupNo'], 'int', 'Group No', true);

    if (
        $questionMstrId -> valid == 1 && $sessionId -> valid == 1 && $employeeId -> valid == 1 && 
        $pageNo -> valid == 1 && $groupNo -> valid == 1
    ) {
        $questionDetails = QuestionDtl::getByPage(array(
            "questionMstrId" => $questionMstrId -> value,
            "groupNo" => $groupNo -> value,
            "pageNo" => $pageNo -> value
        ));

        $response['contentType'] = 'dynamic-content';
        $response['content']['form'] = "
            <div class='col-10 offset-1 margin-top-md margin-bottom-xs' style='font-weight: 600'>
                Question :
            </div>
            {$questionDetails[0]['question']}
        ";

        $response['content']['form'] .= QuestionDtl::getFormSection(array(
            'questionMstrId' => $questionMstrId -> value,
            'questionSessionId' => $sessionId -> value,
            'employeeId' => $employeeId -> value,
            'groupNo' => $groupNo -> value,
            'pageNo' => $pageNo -> value,
            'error' => array(
                "hasError" => 0,
                "errorMessage" => ''
            )
        ));
    } else {
        $errorMessage = '';
        if ($questionMstrId -> valid == 0) {
            $errorMessage = $questionMstrId -> err_msg;
        } else if ($sessionId -> valid == 0) {
            $errorMessage = $sessionId -> err_msg;
        } else if ($employeeId -> valid == 0) {
            $errorMessage = $employeeId -> err_msg;
        } else if ($pageNo -> valid == 0) {
            $errorMessage = $pageNo -> err_msg;
        } else if ($groupNo -> valid == 0) {
            $errorMessage = $groupNo -> err_msg;
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