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


if (isset($_POST['questionMstrId'])) {
    $questionMstrId = new form_validation($_POST['questionMstrId'], 'int', 'Question Master ID', true);

    if ($questionMstrId -> valid == 1) {
        // Verify if the Question Master ID is valid
        $survey = QuestionMstr::show($questionMstrId -> value);
        if (is_null($survey)) {
            $questionMstrId -> valid = 0;
            $questionMstrId -> err_msg = "Question Master ID not found. <br> Please refresh this page.";
        }
    }

    if ($questionMstrId -> valid == 1) {
        // Set the Content Type to Dynamic Content
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
                        <input type='text' name='employeeId' id='employeeId' class='form-control' placeholder='0000-0000'/>
                    </div>
                </div>
                <div class='form-row'>
                    <label class='col-sm-8 offset-sm-2' for='birthDate'>Birthdate: </label>
                    <div class='col-sm-8 offset-sm-2'>
                        <div class='form-control form-control-line' data-toggle='modal' data-target='#datepicker-modal'>
                            <span style='color: #868E96'>Select Date</span>            
                        </div>
                        <input type='date' name='birthDate' id='birthDate' hidden/>
                    </div>
                </div>
                <div class='col-6 offset-3 text-center margin-top-sm'>
                    <button type='button' class='btn btn-info w-100 form-submit-button'>Proceed</button>
                </div>
            </form>

            </div>
        ";
    } else {
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
    }
} else {
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