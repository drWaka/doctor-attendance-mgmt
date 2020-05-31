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
        $questionMstrQry = "SELECT PK_questionMstr FROM questionMstr WHERE PK_questionMstr = '{$questionMstrId -> value}'";
        $questionMstrRes = $connection -> query($questionMstrQry);

        if (!($questionMstrRes -> num_rows > 0)) {
            $questionMstrId -> valid = 0;
            $questionMstrId -> err_msg = "Question Master ID not found. <br> Please refresh this page.";
        }
    }

    if ($questionMstrId -> valid == 1 && $employeeId -> valid == 1 && $birthDate -> valid == 1) {
        // Check if the Employee ID & Birthdate are valid
        $employeeQry = "SELECT * FROM employees WHERE employeeNo = '{$employeeId -> value}' AND birthdate = '{$birthDate -> value}'";
        $employeeRes = $connection -> query($employeeQry);
        
        if (!($employeeRes -> num_rows > 0)) {
            // Use Question Master ID to Trigger modal instead of Form errors
            $employeeId -> valid = 0;
            $employeeId -> err_msg = 'flag';
            $birthDate -> valid = 0;
            $birthDate -> err_msg = 'Employee record not found';
        } else {
            $employeeRec = $employeeRes -> fetch_object();
            $parameters['employeeId'] = $employeeRec -> PK_employee;
        }
    }

    if ($questionMstrId -> valid == 1 && $employeeId -> valid == 1 && $birthDate -> valid == 1) {
        // Check if the employe has already an existing survey session dated today
        $startDate = date('Y-m-d') . " 00:00:00";
        $endDate = date('Y-m-d') . " 23:59:59";
        $questionResponseQry = "
            SELECT * FROM questionSession
            WHERE sessionDate BETWEEN '{$startDate}' AND '{$endDate}' 
            LIMIT 1
        ";
        $questionResponseRes = $connection -> query($questionResponseQry);

        if ($questionResponseRes -> num_rows > 0) {
            $questionResponseRec = $questionResponseRes -> fetch_assoc();

            $parameters['questionSessionId'] = $questionResponseRec['PK_questionSession'];
            $parameters['isDone'] = $questionResponseRec['isDone'];
        } else {
            // Create new Session
            $insertQuestionSession = "
                INSERT INTO questionSession (FK_questionMstr, FK_employee)
                VALUES ('{$parameters['questionMstrId']}', '{$parameters['employeeId']}')
            ";
            if (!($connection -> query($insertQuestionSession))) {
                $flags['hasError'] = 1;

                $response['success'] = 'failed';
                $response['contentType'] = 'modal';
                $response['content']['modal'] = modalize(
                    '<div class="row text-center">
                        <h2 class="header capitalize col-12">System Error Encoutered</h2>
                        <p class="para-text col-12">Error Details: Unable to initialize Survey Session. <br> Please contact your system administrator.</p>
                    </div>', 
                    array(
                        "trasnType" => 'error',
                        "btnLbl" => 'Dismiss'
                    )
                );   
            }
            $parameters['questionSessionId'] = $connection -> insert_id;

            // Insert Reponse Records
            $questionsQry = "
                SELECT a.* 
                FROM questionDtl AS a
                INNER JOIN questionGrp AS b ON a.FK_questionGrp = b.PK_questionGrp
                WHERE a.FK_questionMstr = '{$parameters['questionMstrId']}'
                    AND a.isDeleted = 0
                ORDER BY b.sorting, a.sorting
            ";
            $questionsRes = $connection -> query($questionsQry);

            if ($questionsRes -> num_rows > 0) {
                while ($questionsRec = $questionsRes -> fetch_assoc()) {
                    $insertQuestionResponse = "
                        INSERT INTO questionResponse (
                            FK_questionMstr, FK_questionSession, 
                            FK_questionDtl, FK_employee
                        ) VALUES (
                            '{$parameters['questionMstrId']}', '{$parameters['questionSessionId']}', 
                            '{$questionsRec['PK_questiondtl']}', '{$parameters['employeeId']}'
                        )
                    ";
                    
                    // die($insertQuestionResponse);
                    if (!($connection -> query($insertQuestionResponse))) {
                        $flags['hasError'] = 1;

                        $response['success'] = 'failed';
                        $response['contentType'] = 'modal';
                        $response['content']['modal'] = modalize(
                            '<div class="row text-center">
                                <h2 class="header capitalize col-12">System Error Encoutered</h2>
                                <p class="para-text col-12">Error Details: Unable to generate survey questions. <br> Please contact your system administrator.</p>
                            </div>', 
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
                    <div class='col-10 offset-1 margin-top-md margin-bottom-xs' style='font-weight: 600'>
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
                <div class='col-6 offset-3 text-center'>
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