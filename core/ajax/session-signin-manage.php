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


if (isset($_POST['questionMstrId']) && isset($_POST['employeeId']) && isset($_POST['birthDate'])) {
    $questionMstrId = new form_validation($_POST['questionMstrId'], 'int', 'Question Master ID', true);
    $employeeId = new form_validation($_POST['employeeId'], 'str-int', 'Employee ID', true);
    $birthDate = new form_validation($_POST['birthDate'], 'date', 'Birthdate', true);

    $parameters = array(
        "questionMstrId" => $questionMstrId -> value,
        "questionGrpId" => '1',
        "questionSessionId" => '',
        "employeeId" => ''
     );

    $flags = array(
        'hasError' => 0,
        'isDone' => 0
    );

    if ($questionMstrId -> valid == 1 && $employeeId -> valid == 1 && $birthDate -> valid == 1) {
        // Verify if the Question Master ID is valid
        $survey = QuestionMstr::show($questionMstrId -> value);
        if (is_null($survey)) {
            $questionMstrId -> valid = 0;
            $questionMstrId -> err_msg = "Question Master ID not found. <br> Please refresh this page.";
        }
    }

    if ($questionMstrId -> valid == 1 && $employeeId -> valid == 1 && $birthDate -> valid == 1) {
        // Check if the Employee ID & Birthdate are valid
        $employee = Employee::getByEmployeeNo($employeeId -> value);
        if (!(count($employee) > 0)) {
            $employeeId -> valid = 0;
            $employeeId -> err_msg = 'Employee Id is Invalid';
        } else {
            $employeeBirthdate = Employee::getByBirthdate(array(
                "employeeId" => $employee[0]['PK_employee'],
                "birthdate" => $birthDate -> value
            ));
            if (!(count($employeeBirthdate) > 0)) {
                $birthDate -> valid = 0;
                $birthDate -> err_msg = 'Birthdate is Incorrect';
            } else {
                $parameters['employeeId'] = $employee[0]['PK_employee'];
            }
        }
    }

    if ($questionMstrId -> valid == 1 && $employeeId -> valid == 1 && $birthDate -> valid == 1) {
        // Check if the employe has already an existing survey session dated today
        $employeeSession = QuestionSession::hasSession(array(
            "employeeId" => $parameters['employeeId'],
            "sessionDate" => date('Y-m-d'),
            "questionMsrtId" => $questionMstrId -> value
        ));

        if (count($employeeSession) > 0) {
            $parameters['questionSessionId'] = $employeeSession[0]['PK_questionSession'];
            $flags['isDone'] = $employeeSession[0]['isDone'];
        } else {
            // Create new Session
            $sessionCreate = QuestionSession::create(array(
                "questionMstrId" => $parameters['questionMstrId'],
                "employeeId" => $parameters['employeeId']
            ));
            if ($sessionCreate['hasError'] == 1) {
                $flags['hasError'] = 1;

                $response['success'] = 'failed';
                $response['contentType'] = 'modal';
                $response['content']['modal'] = modalize(
                    "<div class='row text-center'>
                        <h2 class='header capitalize col-12'>System Error Encoutered</h2>
                        <p class='para-text col-12'>Error Details: {$sessionCreate['errorMessage']}</p>
                    </div>", 
                    array(
                        "trasnType" => 'error',
                        "btnLbl" => 'Dismiss'
                    )
                );   
            }
            $parameters['questionSessionId'] = $connection -> insert_id;

            $surveyQuestions = QuestionDtl::getByQuestionMstr($parameters['questionMstrId']);
            // die(var_dump($surveyQuestions));

            if (count($surveyQuestions) > 0) {
                foreach ($surveyQuestions AS $surveyQuestion) {
                    // die(var_dump($surveyQuestion));
                    $responseInsert = QuestionResponse::create(array(
                        "questionMstrId" => $parameters['questionMstrId'],
                        "questionSessionId" => $parameters['questionSessionId'],
                        "PK_questiondtl" => $surveyQuestion['PK_questiondtl'],
                        "employeeId" => $parameters['employeeId']
                    ));
                    
                    
                    // die($insertQuestionResponse);
                    if ($responseInsert['hasError'] == 1) {
                        $flags['hasError'] = 1;

                        $response['success'] = 'failed';
                        $response['contentType'] = 'modal';
                        $response['content']['modal'] = modalize(
                            "<div class='row text-center'>
                                <h2 class='header capitalize col-12'>System Error Encoutered</h2>
                                <p class='para-text col-12'>Error Details: {$responseInsert['errorMessage']}</p>
                            </div>", 
                            array(
                                "trasnType" => 'error',
                                "btnLbl" => 'Dismiss'
                            )
                        );

                        break;
                    }
                }
            }
        }

        if ($flags['hasError'] == 0) {
            if ($flags['isDone'] == 0) {
                // Proceed to the survey
                $questionDetails = QuestionDtl::getByPage(array(
                    "questionMstrId" => $parameters['questionMstrId'],
                    "groupNo" => '1',
                    "pageNo" => '1'
                ));

                $response['contentType'] = 'dynamic-content';
                $response['content']['form'] = "
                    <div class='col-12 margin-top-md margin-bottom-xs' style='font-weight: 600'>
                        Question :
                    </div>
                    {$questionDetails[0]['question']}
                ";

                $response['content']['form'] .= QuestionDtl::getFormSection(array(
                    'questionMstrId' => $parameters['questionMstrId'],
                    'questionSessionId' => $parameters['questionSessionId'],
                    'employeeId' => $parameters['employeeId'],
                    'groupNo' => '1',
                    'pageNo' => '1',
                    'error' => array(
                        "hasError" => 0,
                        "errorMessage" => ''
                    )
                ));

            } else {
                // Set success to fail to prevent adding multiple no of event at html form
                $response['success'] = 'failed';
                $response['contentType'] = 'modal';
                $response['content']['modal'] = modalize(
                    '<div class="row text-center">
                        <h2 class="header capitalize col-12">Survey already taken</h2>
                        <p class="para-text col-12">You have already finished taking the survey today. <br> Please come back again tommorow.</p>
                    </div>', 
                    array(
                        "trasnType" => 'error',
                        "btnLbl" => 'Dismiss'
                    )
                );   
            }
        }
    } else {
        if ($questionMstrId -> valid == 0) {
            $response['success'] = 'failed';
            $response['contentType'] = 'modal';
            $response['content']['modal'] = modalize(
                "<div class='row text-center'>
                    <h2 class='header capitalize col-12'>System Error Encountered</h2>
                    <p class='para-text col-12'>Error Details: {$questionMstrId -> err_msg}</p>
                </div>", 
                array(
                    "trasnType" => 'error',
                    "btnLbl" => 'Dismiss'
                )
            );
        } else {
            $employeeIdErr = new error_handler($employeeId -> err_msg);
            $birthDateErr = new error_handler($birthDate -> err_msg);


            $response['contentType'] = 'dynamic-content';
            $response['content']['form'] = "
            <div class='text-center'>
                Please provide the details below for us to indentify you.
            </div>
            <div class=''>
            <form class='margin-top-sm transaction-form' 
                action='core/ajax/session-signin-manage.php'
                method='POST'
                tran-type='async-form'
                tran-container='dynamic-content'
                form-name='login-form'
                submit-type='asynchronous'
            >   
                <input type='text' name='questionMstrId' hidden='hidden' value='{$questionMstrId -> value}'/>
                <div class='form-row'>
                    <label class='col-sm-8 offset-sm-2' for='employeeId'>Employee ID: </label>
                    <div class='col-sm-8 offset-sm-2'>
                        <input type='text' name='employeeId' id='employeeId' class='form-control {$employeeIdErr -> error_class}' value='{$employeeId -> value}'placeholder='0000-0000'/>
                        {$employeeIdErr -> error_icon}
                        {$employeeIdErr -> error_text}
                    </div>
                </div>
                <div class='form-row'>
                    <label class='col-sm-8 offset-sm-2' for='birthDate'>Birthdate: </label>
                    <div class='col-sm-8 offset-sm-2'>
                        <input type='date' name='birthDate' id='birthDate' class='form-control {$birthDateErr -> error_class}' value='{$birthDate -> value}' />
                        {$birthDateErr -> error_icon}
                        {$birthDateErr -> error_text}
                    </div>
                </div>
                <div class='col-6 offset-3 text-center margin-top-sm'>
                    <button type='button' class='btn btn-info w-100 form-submit-button'>Proceed</button>
                </div>
            </form>

            </div>
            ";
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