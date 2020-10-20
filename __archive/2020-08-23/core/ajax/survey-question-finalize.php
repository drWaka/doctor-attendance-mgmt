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
    isset($_POST['questionMstrId']) && 
    isset($_POST['sessionId']) && 
    isset($_POST['employeeId'])
) {
    $questionMstrId = new form_validation($_POST['questionMstrId'], 'int', 'Question Master ID', true);
    $sessionId = new form_validation($_POST['sessionId'], 'int', 'Session ID', true);
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Employee ID', true);

    if ($questionMstrId -> valid == 1 && $sessionId -> valid == 1 && $employeeId -> valid == 1) {
        // Update Survey Session isDone Flag
        $totalRate = QuestionResponse::getTotalRate($sessionId -> value);
        $surveyRemarks = QuestionRating::getRemarksByRate(array(
            "rate" => $totalRate,
            "questionMstrId" => $questionMstrId -> value
        ));
        $finalization = QuestionSession::finalizeSession(array(
            "totalRate" => $totalRate,
            "remarks" => $surveyRemarks,
            "questionSessionId" => $sessionId -> value
        ));

        // die(var_dump(isset($finalization['isSuccess'])));
        if ($finalization['isSuccess'] == 1) {
            // Proceed to Finish Survey
            $response['contentType'] = 'dynamic-content';
            $response['content']['form'] = "
                <div class='text-center margin-top-lg'>
                    You have successfully finished the survey. Click the button below to return to homepage.
                </div>
                <div class='margin-top-md'>
                <div class='col-6 offset-3 text-center'>
                    <a href='index.php?surveyId={$questionMstrId -> value}'>
                        <button type='button' class='btn btn-info w-100 form-submit-button'><i class='fa fa-house'></i> Home</button>
                    </a>
                </div>

                </div>
            ";
        } else {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$finalization['errorMessage']}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }
        
    } else {
        $errorMessage = '';
        if ($questionMstrId -> valid == 0) {
            $errorMessage = $questionMstrId -> err_msg;
        } else if ($sessionId -> valid == 0) {
            $errorMessage = $sessionId -> err_msg;
        } else if ($employeeId -> valid == 0) {
            $errorMessage = $employeeId -> err_msg;
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