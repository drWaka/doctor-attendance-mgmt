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
    isset($_POST['pageNo']) && isset($_POST['groupNo']) && isset($_POST['response']) &&
    isset($_POST['dataType']) && isset($_POST['isRequired']) && isset($_POST['desc'])
) {
    $questionMstrId = new form_validation($_POST['questionMstrId'], 'int', 'Question Master ID', true);
    $sessionId = new form_validation($_POST['sessionId'], 'int', 'Session ID', true);
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Employee ID', true);
    $pageNo = new form_validation($_POST['pageNo'], 'int', 'Page No', true);
    $groupNo = new form_validation($_POST['groupNo'], 'int', 'Group No', true);

    $dataType = new form_validation($_POST['dataType'], 'str', 'Field Data Type', true);
    $isRequired = new form_validation($_POST['isRequired'], 'int', 'Field IsRequired', true);
    $desc = new form_validation($_POST['desc'], 'str-int', 'Field Description', true);
    if ($dataType -> valid == 1 && $isRequired -> valid == 1 && $desc -> valid == 1 ) {
        $isFieldRequired = (strtolower($isRequired -> value) == '1')
            ? true
            : false;
        $responseVal = new form_validation(
            $_POST['response'],
            $dataType -> value,
            $desc -> value,
            $isFieldRequired
        );
    } else {
        $responseVal = new stdClass();
        $responseVal -> value = '';
        $responseVal -> valid = 0;
        $responseVal -> err_msg = 'Unable to parse Response properties. <br> Please contact your system administrator';
    }

    if (
        $questionMstrId -> valid == 1 && $sessionId -> valid == 1 && $employeeId -> valid == 1 && 
        $pageNo -> valid == 1 && $groupNo -> valid == 1 && $responseVal -> valid == 1
    ) {
        // Get Current Question Detail ID
        $activeQuestionId = QuestionDtl::getQuestionDetailId(array(
            "questionMstrId" => $questionMstrId -> value,
            "groupNo" => $groupNo -> value,
            "pageNo" => $pageNo -> value
        ));
        // Check if the question response has custom redirection
        $customPage = QuestionDtlOption::checkRedirection(array(
            "questionDtlId" => $activeQuestionId,
            "responseVal" => $responseVal -> value,
        ));

        // Proceed to next question flag
        $proceedQuestion = true;

        // Next Question Parameters
        $next = array(
            "groupNo" => $groupNo -> value,
            "pageNo" => (intval($pageNo -> value) + 1)
        );

        // validate if response has custom redirection
        if ($customPage['hasRedirection'] == 1) {
            // Override Normal Pagination
            $next = array(
                "groupNo" => $customPage['groupNo'],
                "pageNo" => $customPage['pageNo']
            );
        }

        // Start ToDo : Include in a Loop
        // Get Question Detail ID
        $questionIdFlag = QuestionDtl::getQuestionDetailId(array(
            "questionMstrId" => $questionMstrId -> value,
            "groupNo" => $next['groupNo'],
            "pageNo" => $next['pageNo']
        ));
        
        if ($questionIdFlag == 0) {
            $next['groupNo'] = (intval($next['groupNo']) + 1);
            $next['pageNo'] = 1;
            // Get Question Detail ID
            $questionIdFlag = QuestionDtl::getQuestionDetailId(array(
                "questionMstrId" => $questionMstrId -> value,
                "groupNo" => $next['groupNo'],
                "pageNo" => $next['pageNo']
            ));

            if ($questionIdFlag == 0) {
                $proceedQuestion = false;
            }
        }
        // End ToDo : Include in a Loop

        // Update Response
        $responseUpdate = QuestionResponse::updateResponse(array(
            "response" => $responseVal -> value,
            "questionMsrtId" => $questionMstrId -> value,
            "questionSessionId" => $sessionId -> value,
            "questionDtl" => $activeQuestionId,
            "employeeId" => $employeeId -> value
        ));

        if ($responseUpdate['error'] == 0) {
            if ($proceedQuestion) {
                // Get Next Survey Question
                $questionDetails = QuestionDtl::getByPage(array(
                    "questionMstrId" => $questionMstrId -> value,
                    "groupNo" => $next['groupNo'],
                    "pageNo" => $next['pageNo']
                ));

                $response['contentType'] = 'dynamic-content';
                $response['content']['form'] = "
                    <div class='col-12 margin-top-md margin-bottom-xs' style='font-weight: 600'>
                        Question :
                    </div>
                    {$questionDetails[0]['question']}
                ";

                $response['content']['form'] .= QuestionDtl::getFormSection(array(
                    'questionMstrId' => $questionMstrId -> value,
                    'questionSessionId' => $sessionId -> value,
                    'employeeId' => $employeeId -> value,
                    'groupNo' => $next['groupNo'],
                    'pageNo' => $next['pageNo'],
                    'error' => array(
                        "hasError" => 0,
                        "errorMessage" => ''
                    )
                ));
            } else {
                // Show Survey Summary
                // Get Max Group & Page Nos for turning back button
                $max = array();
                $max["group"] = QuestionGrp::getMaxGroupNo($questionMstrId -> value);
                $max["page"] = QuestionDtl::getMaxPageNo(array(
                    "questionMstrId" => $questionMstrId -> value,
                    "groupNo" => $max["group"]
                ));

                // Session Question Responses
                $responses = "";
                $sessionResponses = QuestionResponse::getBySessionId($sessionId -> value);
                $counter = 1;
                foreach($sessionResponses as $value) {
                    $question = QuestionDtl::show($value['FK_questionDtl']);
                    if (count($question) > 0) {
                        $question = $question[0];
                    } else {
                        $question = array(
                            "question" => ''
                        );
                    }

                    $questionResponse = !empty($value['response']) 
                        ? $value['response']
                        : '-';
                    $responses .= "
                    <div class='col-12'>Question #{$counter}</div>
                    <div class='12' style='padding-left: 15px;'>
                        <div class='row'>
                            {$question['question']}
                        </div>
                    </div>
                    <div class='col-12 margin-bottom'>Response : <b class='capitalize'>{$questionResponse}</b></div>
                    ";
                    $counter++;
                }

                $response['contentType'] = 'dynamic-content';
                $response['content']['form'] = "
                    <div class='row'>
                        <div class='col-12 margin-top-sm'>
                            Please review your responses for finalization:
                        </div>
                    </div>
                    <div class='form-row margin-top-xs'>
                        {$responses}
                    </div>

                    <div class='form-row margin-top-sm'>
                        <div class='col-4 offset-2 text-center'>
                            <button type='button' class='btn btn-info w-100 transaction-button'
                                tran-type='async-form'
                                tran-link='core/ajax/survey-question-back.php'
                                tran-data='{
                                    &quot;questionMstrId&quot; : &quot;{$questionMstrId -> value}&quot;,
                                    &quot;sessionId&quot; : &quot;{$sessionId -> value}&quot;,
                                    &quot;employeeId&quot; : &quot;{$employeeId -> value}&quot;,
                                    &quot;pageNo&quot; : &quot;{$max["page"]}&quot;,
                                    &quot;groupNo&quot; : &quot;{$max["group"]}&quot;
                                }'
                                tran-container='dynamic-content'
                            >Back</button>
                        </div>
                        <div class='col-4 text-center'>
                            <button type='button' class='btn btn-info w-100 transaction-button'
                                tran-type='async-form'
                                tran-link='core/ajax/survey-question-finalize.php'
                                tran-data='{
                                    &quot;questionMstrId&quot; : &quot;{$questionMstrId -> value}&quot;,
                                    &quot;sessionId&quot; : &quot;{$sessionId -> value}&quot;,
                                    &quot;employeeId&quot; : &quot;{$employeeId -> value}&quot;
                                }'
                                tran-container='dynamic-content'
                            >Finalize</button>
                        </div>
                    </div>
                ";
            }
        } else {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$responseUpdate['errorMessage']}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        }

    } else {
        if (
            $questionMstrId -> valid == 0 || $sessionId -> valid == 0 || 
            $employeeId -> valid == 0 || $pageNo -> valid == 0 ||
            $groupNo -> valid == 0 
        ) {
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
        } else {
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
                'groupNo' => '1',
                'pageNo' => '1',
                'error' => array(
                    "hasError" => 1,
                    "errorMessage" => $responseVal -> err_msg
                )
            ));
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