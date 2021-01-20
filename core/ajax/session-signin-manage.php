<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// MPDF Aliases
use \Mpdf\Mpdf;
use \Mpdf\HTMLParserMode;

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
                // Is Generate Hospital Pass Feature is Enabled
                $hospitalPassMessage = '';
                $hospitalPassMailingStatus = SystemFeatures::isFeatureEnabled('GEN_HOSP_PASS');

                if ($hospitalPassMailingStatus == true) {
                    // Generate Gate Pass PDF
                    $employee = Employee::getByEmployeeNo($employeeId -> value);
                    $employeeNo = Employee::show($employee[0]['PK_employee']);

                    $file = array(
                        "path" => __DIR__ . '/../files/pdf/',
                        "file" => date('Y-m-d') . "-" . $employeeNo['employeeNo'],
                        "weblink" => $_SERVER['PHP_SELF'] . '/../../files/pdf/' . date('Y-m-d') . "-" . $employeeNo['employeeNo'] . '.pdf'
                    );

                    if (!file_exists($file['path'] . $file['file'] . '.pdf')) {
                        $pdfObj = new Mpdf([
                            'tempDir' => $file['path']
                        ]);
                        $cssFiles = array(
                            // 'template' => file_get_contents(__DIR__ . '/../../vendor/admin4b/css/admin4b.min.css'),
                            "custom" => file_get_contents(__DIR__ . '/../css/pdf-template-style.css')
                        );
        
                        // Day of the Week
                        $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        // Employee Name
                        $middleInitial = (!is_null($employeeNo['middleName']) || !empty($employeeNo['middleName']))
                            ? substr($employeeNo['middleName'], 0, 1) . '.'
                            : '';
                        $employeeName = $employeeNo['firstName'] . ' ' . $middleInitial .  ' ' . $employeeNo['lastName'];
                        // Department
                        $department = MscDepartment::show($employeeNo['FK_mscDepartment']);
                        $department = $department['description'];
                        // Division
                        $division = MscDivision::show($employeeNo['FK_mscDivision']);
                        $division = $division['description'];
        
                        // Health Status
                        $healthStatus = QuestionSession::getSessionByEmpDate(array(
                            "questionMstrId" => $questionMstrId -> value,
                            "employeeId" => $employeeNo['PK_employee'],
                            "sessionDate" => array(
                                'start' => date('Y-m-d') . ' 00:00:00',
                                'end' => date('Y-m-d') . ' 23:59:59'
                            )
                        ));
                        // die(var_dump($healthStatus));
                        $color = 'green';
                        if (intval($healthStatus['totalRate']) > 0) $color = 'red';
                        $hospitalPassStatus = "<span style='color:{$color}'>{$healthStatus['remarks']}</span>";
                        
                        $validityDate = new DateTime(date('Y-m-d', strtotime($healthStatus['sessionDate'])));
                        // $validityDate -> add(new DateInterval('P1D'));
                        $html = '
                            <div class="row">
                                <div class="col-6 text-left">
                                    
                                </div>
                            </div>
                            <div class="row header">
                                <div class="col-12 logo-container text-center">
                                    <img src="../img/ollh-logo.gif" alt="ollh-logo" style="width: 8%;">
                                </div>
                                <div class="col-12 header-text text-center">
                                    Our Lady of Lourdes Hospital <br>
                                    eTriage Hospital Pass
                                </div>
                            </div>
            
                            <div class="row text-center header">
                                <div class="col-12 header-text margin-top">Health Declaration Status : </div>
                                <div class="col-12">
                                    <h2 class="uppercase health-status first">
                                        ' . $hospitalPassStatus . '<br>
                                        <small>Valid on:</small> <small style="color: blue">' . $validityDate -> format('l') . $validityDate -> format(', F d, Y') . '</small>
                                    </h2>
                                    <h2 class="health-status"><b>' . $employeeNo['employeeNo'] . ' &minus; ' . $employeeName . '</b></h2>
                                </div>
                                <div class="col-12">' . $department . '</div>
                                <div class="col-12">' . $division . '</div>
                            </div>
            
                            <p class="help-text text-center">For inquiries and concerns please email us at compliance@ollh.ph</p>
                            <p class="help-text text-center"><b>Hospital Pass # :</b> ' . $healthStatus['PK_questionSession'] . '</p>
                        ';
                        // PDF File Creation
                        $file['path'] = $file['path'] . $file['file'] . '.pdf';
                        $pdfObj->WriteHTML($cssFiles['custom'], HTMLParserMode::HEADER_CSS);
                        $pdfObj->WriteHTML($html, HTMLParserMode::HTML_BODY);
                        $pdfObj->Output($file['path'], 'F');
                    }

                    $hospitalPassMessage = '<p class="para-text col-10 offset-1">Wanted to download your latest Hospital Pass? Please click <a target="_blank" href="' . $file['weblink'] . '">here</a>.</p>';
                }
                

                // Set success to fail to prevent adding multiple no of event at html form
                $response['success'] = 'failed';
                $response['contentType'] = 'modal';
                $response['content']['modal'] = modalize(
                    '<div class="row text-center">
                        <h2 class="header capitalize col-12">Survey already taken</h2>
                        <p class="para-text col-12">You have already finished taking the survey today. <br> Please come back again tommorow.</p>
                        ' . $hospitalPassMessage . '
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

            $dayOfTheWeek = "";
            switch (date('N', strtotime($birthDate -> value))) {
                case '1':
                    $dayOfTheWeek = 'Monday';
                    break;
                case '2':
                    $dayOfTheWeek = 'Tuesday';
                    break;
                case '3':
                    $dayOfTheWeek = 'Wednesday';
                    break;
                case '4':
                    $dayOfTheWeek = 'Thursday';
                    break;
                case '5':
                    $dayOfTheWeek = 'Friday';
                    break;
                case '6':
                    $dayOfTheWeek = 'Saturday';
                    break;
                case '7':
                    $dayOfTheWeek = 'Sunday';
                    break;
                
                default:
                    # code...
                    break;
            }
            $birthdateValue = empty($birthDate -> value)
                ? "<span style='color: #868E96'>Select Date</span>"
                : $dayOfTheWeek . ", " . date('M', strtotime($birthDate -> value)) . " " . ltrim(date('d', strtotime($birthDate -> value)), '0') . " " . date('Y', strtotime($birthDate -> value));
            
            $datepickerElement = '';
            if ($_POST['browser'] == 'chrome' || $_POST['browser'] == 'mozilla') {
                $datepickerElement = "
                    <div class='form-control form-control-line {$birthDateErr -> error_class}' data-toggle='modal' data-target='#datepicker-modal'>
                        <span style='color: #868E96'>{$birthdateValue}</span>            
                    </div>
                    {$birthDateErr -> error_icon}
                    {$birthDateErr -> error_text}
                    <input type='date' name='birthDate' id='birthDate' value='{$birthDate -> value}' hidden/>
                ";
            } else {
                $datepickerElement = "
                    <input type='date' name='birthDate' class='form-control form-control-line' id='birthDate' value='{$birthDate -> value}'/>
                ";
            }

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
                <input type='text' name='browser' hidden='hidden' value='{$_POST['browser']}'/>
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
                        {$datepickerElement}
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