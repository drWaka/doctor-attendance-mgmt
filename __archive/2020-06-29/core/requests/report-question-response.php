<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

if (
    isset($_POST['csvEmployeeName']) && 
    isset($_POST['csvSessionDate']) && 
    isset($_POST['csvQuestionMstrId'])
) {
    $questionMstrId = new form_validation($_POST['csvQuestionMstrId'], 'int', 'Question Master ID', true);
    $employeeName = new form_validation($_POST['csvEmployeeName'], 'str-int', 'Employee Name', false);
    $sessionDate = new form_validation($_POST['csvSessionDate'], 'date', 'Session Date', true);

    if (
        $questionMstrId -> valid == 1 && $employeeName -> valid == 1 && $sessionDate -> valid == 1
    ) {
        // Determine if the Question Master Id exists
        $question = QuestionMstr::show($questionMstrId -> value);

        if (!(count($question) > 0)) {
            $questionMstrId -> valid = 0;
            $questionMstrId -> err_msg = 'Question Master ID is invalid';
        }
    }

    if (
        $questionMstrId -> valid == 1 && $employeeName -> valid == 1 && $sessionDate -> valid == 1
    ) {
        $csvContent = array();
        // CSV Header
        $questions = QuestionDtl::getByQuestionMstr($questionMstrId -> value);
        $csvContent[0] = array(
            'Employee No',
            'Employee Name',
            'Survey Name',
            'Survey Date',
            'Summary'
        );
        if (count($questions) > 0) {
            foreach($questions as $question) {
                $csvContent[0][count($csvContent[0])] = strip_tags($question['question']);
            }
        }


        $employeeQuery = "
            SELECT * FROM employees
            WHERE 
                CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), '. ', lastName) LIKE '%{$employeeName -> value}%'

                OR CONCAT(lastName, ', ', firstName, ' ', middleName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName, ' ', SUBSTR(middleName, 1, 1)) LIKE '%{$employeeName -> value}%'
                OR employeeNo LIKE '%{$employeeName -> value}%'
        ";
        $employeeResult = $connection -> query($employeeQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_ASSOC);

        if (count($employeeRecords) > 0) {
            foreach ($employeeRecords as $employeeRecord) {
                // CSV Body
                $arrayIndex = count($csvContent);
                $csvContent[$arrayIndex] = array();

                // Employee Details
                $middleInitial = !empty($employeeRecord['middleName'])
                    ? substr($employeeRecord['middleName'], 0, 1)
                    : '';
                $employeeName = strtoupper(("{$employeeRecord['lastName']}, {$employeeRecord['firstName']} {$middleInitial}."));

                $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $employeeRecord['employeeNo'];
                $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $employeeName;

                // Survey Description
                $questionMstr = QuestionMstr::show($questionMstrId -> value);
                if (count($questionMstr) > 1) {
                    $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $questionMstr['title'];
                } else {
                    $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = '-';
                }

                // Survey Summary
                $questionSession = QuestionSession::getSessionByEmpDate(array(
                    "employeeId" => $employeeRecord['PK_employee'],
                    "questionMstrId" => $questionMstrId -> value,
                    "sessionDate" => $sessionDate -> value
                ));

                $questionSessionId = 0;
                if (count($questionSession) > 0) {
                    $sessionDateVal = date('F d, Y', strtotime($questionSession['sessionDate']));
                    $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $sessionDateVal;
                    $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $questionSession['remarks'];

                    $questionSessionId = $questionSession['PK_questionSession'];
                } else {
                    $sessionDateVal = date('F d, Y', strtotime($sessionDate -> value));
                    // No Response Summary Found
                    $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $sessionDateVal;
                    $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = '-';
                }

                // Survey Responses
                $questions = QuestionDtl::getByQuestionMstr($questionMstrId -> value);
                if (count($questions) > 1) {
                    foreach($questions as $question) {
                        $questionResponse = QuestionResponse::getResponseByNo(array(
                            "questionMstrId" => $questionMstrId -> value,
                            "emplyeeId" => $employeeRecord['PK_employee'],
                            "questionDtlId" => $question['PK_questiondtl'],
                            "questionSessionId" => $questionSessionId
                        ));

                        if (empty($questionResponse)) {
                            $questionResponse = '-';
                        }
                        $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $questionResponse;
                    }
                }

            }
        }

        $currentTimeStamp = strval(strtotime(date("Y-m-d h:i:s")));
        // die(date("Y-m-d H:i:s"));
        $filePath = "../files/csv/{$currentTimeStamp}.csv";
        $csvReport = fopen($filePath, 'w');

        foreach ($csvContent as $row) {
            fputcsv($csvReport, $row);
        }

        fclose($csvReport);
        //die(exec('whoami'));
        // File Name
        $fileName = 'COVID-19 Report File.csv';
        
        // HTTP Headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header("Content-Disposition: attachment; filename=\"{$fileName}\";");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        ob_clean();
        flush();
        readfile($filePath);

    } else {
        $errorMessage = '';
        if ($questionMstrId -> valid == 0) {
            $errorMessage = $questionMstrId -> err_msg;
        } else if ($employeeName -> valid == 0) {
            $errorMessage = $employeeName -> err_msg;
        } else if ($sessionDate -> valid == 0) {
            $errorMessage = $sessionDate -> err_msg;
        }

        die('System Error Encountered: ' . $errorMessage);
    }

} else {
    die('System Error Encountered: Insufficient Data Submitted<br/> Please contact your System Administrator');
}