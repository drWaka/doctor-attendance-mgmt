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
            // Generate Gate Pass PDF
            $employeeNo = Employee::show($employeeId -> value);
            $file = array(
                "path" => __DIR__ . '/../files/pdf/',
                "file" => date('Y-m-d') . "-" . $employeeNo['employeeNo'],
                "weblink" => $_SERVER['PHP_SELF'] . '/../../files/pdf/' . date('Y-m-d') . "-" . $employeeNo['employeeNo'] . '.pdf'
            );

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
            $healthStatus = QuestionSession::show($sessionId -> value);
            $color = 'green';
            if (intval($healthStatus[0]['totalRate']) > 0) $color = 'red';
            $healthStatus = "<span style='color:{$color}'>{$healthStatus[0]['remarks']}</span>";

            $html = '
                <div class="row">
                    <div class="col-6 text-left">
                        ' . date('Ymd') . '-' . $sessionId -> value . '
                    </div>
                    <div class="col-6 text-right">
                        ' . $day[date('w')] . date(', F d, Y') . '
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
                        <h2 class="uppercase health-status">' . $healthStatus . '</h2>
                    </div>
                    <div class="col-12 uppercase"><b>' . $employeeNo['employeeNo'] . ' &minus; ' . $employeeName . '</b></div>
                    <div class="col-12">' . $department . '</div>
                    <div class="col-12">' . $division . '</div>
                </div>
            ';
            // PDF File Creation
            $file['path'] = $file['path'] . $file['file'] . '.pdf';
            $pdfObj->WriteHTML($cssFiles['custom'], HTMLParserMode::HEADER_CSS);
            $pdfObj->WriteHTML($html, HTMLParserMode::HTML_BODY);
            $pdfObj->Output($file['path'], 'F');


            // Initialize Email Content
            $emailContent = new stdClass();
            $emailContent -> title = 'OLLH - E-Triage Hospital Pass';
            $emailContent -> mainBody = '
                <div class="container" style="background-color: #FFF;font-family: arial; margin: auto;font-size: 14px;">
                    Dear ' . $employeeName . ', 
                    <br><br>
                        Please refer to the attached PDF File for the E-Triage Hospital Pass.
                    <br><br>
                    <div class="message-middle" style="line-height: 1.3; font-size: 12px;">
                        <span style="font-weight: 700; color: red">Important Notice:</span>
                        <br>
                        Our AGF Security Guards will now require the OLLH Employees, Doctors & other personnel this Hospital Pass upon entry. Failure to comply will result to traditional triage process that is being practiced at our Emergency Room Area.
                    </div>  
                        
                </div>

                <div style="font-family: arial; font-size: 12px;">
                    <br>
                    This is a system generated message. Do not reply to this message. 
                    <br>
                    You are receiving this e-mail as a result of the usage of Our Lady of Lourdes Hospital &minus; E-Triage Application.
                </div>
                <br><br><br><br>
            ';
            $emailContent -> alternateBody = '';
            // File Attachments
            $emailContent -> attachments = array(
                array(
                    "path" => $file['path'],
                    "fileName" => 'ollh-etriage-hospital-pass-' . date('Ymd') . '-' . $sessionId -> value . '.pdf'
                )
            );
            // Recipients
            $reciever['receiver'][0] = new stdClass();
            $reciever['receiver'][0] -> fullname = $employeeName;

            $questionGrpNo = QuestionGrp::getMaxGroupNo($questionMstrId -> value);
            $questionPageNo = QuestionDtl::getMaxPageNo(array(
                "questionMstrId" => $questionMstrId -> value,
                "groupNo" => $questionGrpNo
            ));
            $questionDtl = QuestionDtl::getByPage(array(
                "questionMstrId" => $questionMstrId -> value,
                "groupNo" => $questionGrpNo,
                "pageNo" => $questionPageNo
            ));
            $questionResponse = QuestionResponse::getResponseByNo(array(
                "questionMstrId" => $questionMstrId -> value,
                "emplyeeId" => $employeeId -> value,
                "questionDtlId" => $questionDtl[0]['PK_questiondtl'],
                "questionSessionId" => $sessionId -> value
            ));

            $reciever['receiver'][0] -> email = $questionResponse;

            if (sendEmail($reciever, $emailContent)) {
                // Proceed to Finish Survey
                $response['contentType'] = 'dynamic-content';
                $response['content']['form'] = "
                    <div class='text-center margin-top-lg'>
                        <b><span style='color: red'>Important Notice!</span></b> <br>
                        <p>Starting October 12, 2020, our AGF Security Guards will now require the OLLH Employees, Doctors & other personnel the Hospital Pass upon entry. </p>

                        <p>Hospital Pass will be automatically generated by the E-Triage Application through the email you provided once a <b>Successful E-Triage response</b> has been done.</p>

                        <p>You may also download your latest Hospital Pass <a target='_blank' href='{$file['weblink']}'>here</a>.</p>
                    </div>
                    <div class='text-center margin-top-lg'>
                        You have now successfully finished the survey. Click the button below to return to homepage.
                    </div>
                    <div class='margin-top-md'>
                    <div class='col-6 offset-3 text-center'>
                        <a href='index.php?surveyId={$questionMstrId -> value}'>
                            <button type='button' class='btn btn-info w-100 form-submit-button'><i class='fa fa-house'></i> Home</button>
                        </a>
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