<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

if (
    isset($_POST['csvEmployeeName']) && 
    isset($_POST['csvSessionDate']) && 
    isset($_POST['csvQuestionMstrId']) && 
    isset($_POST['csvSessionRating']) && 
    isset($_POST['csvDivisionId']) && 
    isset($_POST['csvDepartmentId'])
) {
    $questionMstrId = new form_validation($_POST['csvQuestionMstrId'], 'int', 'Question Master ID', true);
    $employeeName = new form_validation($_POST['csvEmployeeName'], 'str-int', 'Employee Name', false);
    $sessionDate = new form_validation($_POST['csvSessionDate'], 'date', 'Session Date', true);
    $divisionId = new form_validation($_POST['csvDivisionId'], 'str-int', 'Division ID', true);
    $departmentId = new form_validation($_POST['csvDepartmentId'], 'str-int', 'Department ID', true);
    $sessionRating = new form_validation($_POST['csvSessionRating'], 'str', 'Session Rating', true);

    if (
        $questionMstrId -> valid == 1 && $employeeName -> valid == 1 && 
        $sessionDate -> valid == 1 && $divisionId -> valid == 1 && 
        $departmentId -> valid == 1 && $sessionRating -> valid == 1
    ) {
        // Determine if the Question Master Id exists
        $question = QuestionMstr::show($questionMstrId -> value);

        if (!(count($question) > 0)) {
            $questionMstrId -> valid = 0;
            $questionMstrId -> err_msg = 'Question Master ID is invalid';
        }
    }

    if (
        $questionMstrId -> valid == 1 && $employeeName -> valid == 1 && 
        $sessionDate -> valid == 1 && $divisionId -> valid == 1 && 
        $departmentId -> valid == 1 && $sessionRating -> valid == 1
    ) {
        $csvContent = array();
        // CSV Header
        $csvContent[0] = array(
            'Our Lady of Lourdes Hospital'
        );

        // Survey Description
        $questionMstr = QuestionMstr::show($questionMstrId -> value);
        if (count($questionMstr) > 1) {
            $csvContent[1] = array(
                $questionMstr['title']
            );
        } else {
            die('Error: Unable to find Survey Record');
        }

        $csvContent[2] = array(
            date('\'F d, Y', strtotime($sessionDate-> value))
        );

        $csvContent[3] = array(
            '',
        );

        $questionQuery = "";
        $questions = QuestionDtl::getByTransctionDate($questionMstrId -> value, $sessionDate-> value);
        if (!(count($questions) > 0)) {
            $questions = QuestionDtl::getByQuestionMstr($questionMstrId -> value);
        }

        // die(var_dump($questions));
        if (count($questions) > 0) {
            $csvContent[4] = array(
                'Employee No',
                'Employee Name',
                'Department',
                'Division', 
                'Summary'
            );

            // Query for Summary Field
            $questionQuery .= ", IFNULL((
                SELECT xa.remarks
                FROM questionsession AS xa
                WHERE xa.FK_employee = a.PK_employee
                    AND xa.FK_questionMstr = {$questionMstrId -> value}
                    AND xa.sessionDate BETWEEN \"{$sessionDate -> value} 00:00:00\" AND \"{$sessionDate -> value} 23:59:59\"
            ), 'No Response') AS summary";
            foreach($questions as $question) {
                $csvContent[4][count($csvContent[4])] = strip_tags($question['question']);

                $questionQuery .= ", IFNULL((
                    SELECT xb.response
                    FROM questionsession AS xa
                    INNER JOIN questionresponse AS xb 
                        ON xa.PK_questionSession = xb.FK_questionSession
                    WHERE xa.FK_employee = a.PK_employee
                        AND xa.FK_questionMstr = {$questionMstrId -> value}
                        AND xb.FK_questionDtl = {$question['PK_questiondtl']}
                        AND xa.sessionDate BETWEEN \"{$sessionDate -> value} 00:00:00\" AND \"{$sessionDate -> value} 23:59:59\"
                ), '-') AS question{$question['PK_questiondtl']}";
            }
        }


        $employeeQuery = "
            SELECT 
                a.employeeNo
                , CONCAT(a.lastName, \", \", a.firstName, \" \", a.middleName) AS `fullName`
                , b.description AS `department`
                , c.description AS `division`
                {$questionQuery}
            FROM employees AS a
            INNER JOIN mscdepartment AS b ON a.FK_mscDepartment = b.PK_mscDepartment
            INNER JOIN mscdivision AS c ON a.FK_mscDivision = c.PK_mscDivision
            WHERE 
                (CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), ' ', lastName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(firstName, ' ', SUBSTR(middleName, 1, 1), '. ', lastName) LIKE '%{$employeeName -> value}%'

                OR CONCAT(lastName, ', ', firstName, ' ', middleName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName) LIKE '%{$employeeName -> value}%'
                OR CONCAT(lastName, ', ', firstName, ' ', SUBSTR(middleName, 1, 1)) LIKE '%{$employeeName -> value}%'
                OR employeeNo LIKE '%{$employeeName -> value}%')
        ";
        if ($sessionRating -> value != 'all') {
            $employeeQuery .= "
                AND LOWER(IFNULL((
                    SELECT xa.remarks
                    FROM questionsession AS xa
                    WHERE xa.FK_employee = a.PK_employee
                        AND xa.FK_questionMstr = {$questionMstrId -> value}
                        AND xa.sessionDate BETWEEN \"{$sessionDate -> value} 00:00:00\" AND \"{$sessionDate -> value} 23:59:59\"
                ), 'No Response')) = '{$sessionRating -> value}'
            ";
        }

        if ($divisionId -> value != 'all' && is_numeric($divisionId -> value)) {
            $employeeQuery .= " AND c.PK_mscDivision = '{$divisionId -> value}'";
        }

        if ($departmentId -> value != 'all' && is_numeric($departmentId -> value)) {
            $employeeQuery .= " AND b.PK_mscDepartment = '{$departmentId -> value}'";
        }
        // die($employeeQuery);
        $employeeResult = $connection -> query($employeeQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_NUM);

        if (count($employeeRecords) > 0) {
            foreach ($employeeRecords as $employeeRecord) {
                // CSV Body
                $arrayIndex = count($csvContent);
                $csvContent[$arrayIndex] = array();

                foreach ($employeeRecord as $column) {
                    $csvContent[$arrayIndex][count($csvContent[$arrayIndex])] = $column;
                }
            }
        }
        // die(var_dump($csvContent));

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
        } else if ($sessionRating -> valid == 0) {
            $errorMessage = $sessionRating -> err_msg;
        } else if ($divisionId -> valid == 0) {
            $errorMessage = $divisionId -> err_msg;
        } else if ($departmentId -> valid == 0) {
            $errorMessage = $departmentId -> err_msg;
        }

        die('System Error Encountered: ' . $errorMessage);
    }

} else {
    die('System Error Encountered: Insufficient Data Submitted<br/> Please contact your System Administrator');
}