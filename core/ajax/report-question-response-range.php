<?php
// Special Cases for Reports
set_time_limit(1000); // Change Max Run Time Limit to 1000 secs

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => '',
        "record" => '',
        "total" => 0
    ),
    "contentType" => ''
);


if (
    isset($_POST['employeeName']) && 
    isset($_POST['sessionStartDate']) && 
    isset($_POST['sessionEndDate']) && 
    isset($_POST['questionMstrId']) &&
    isset($_POST['departmentId']) &&
    isset($_POST['divisionId']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $questionMstrId = new form_validation($_POST['questionMstrId'], 'int', 'Question Master ID', true);
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Employee Name', false);
    $sessionStartDate = new form_validation($_POST['sessionStartDate'], 'date', 'Session Start Date', true);
    $sessionEndDate = new form_validation($_POST['sessionEndDate'], 'date', 'Session End Date', true);
    $departmentId = new form_validation($_POST['departmentId'], 'str-int', 'Department ID', true);
    $divisionId = new form_validation($_POST['divisionId'], 'str-int', 'Division ID', true);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if (
        $questionMstrId -> valid == 1 && $employeeName -> valid == 1 && 
        $sessionStartDate -> valid == 1 && $sessionEndDate -> valid == 1 &&
        $departmentId -> valid == 1 && $divisionId -> valid == 1 &&
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
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
        $sessionStartDate -> valid == 1 && $sessionEndDate -> valid == 1 &&
        $departmentId -> valid == 1 && $divisionId -> valid == 1 &&
        $pageLimit -> valid == 1 && $currentPage -> valid == 1
    ) {
        $surveyResponses = '';
        $dateFlag = new DateTime(date('Y-m-d', strtotime($sessionStartDate -> value)));
        do {
            $alias = explode('-', $dateFlag -> format('Y-m-d'));
            $alias = implode('_', $alias);
            $surveyResponses .= ", IFNULL((
                SELECT xa.remarks
                FROM questionsession AS xa
                WHERE xa.FK_questionMstr = '{$questionMstrId -> value}'
                    AND xa.FK_employee = a.PK_employee
                    AND xa.sessionDate BETWEEN '{$dateFlag -> format('Y-m-d')} 00:00:00' AND '{$dateFlag -> format('Y-m-d')} 23:59:59'
                LIMIT 1
            ), 'No Response') AS `{$alias}`";
            $dateFlag -> add(new DateInterval('P1D'));
        } while (strtotime($dateFlag -> format('Y-m-d')) <= strtotime($sessionEndDate -> value));

        $response['content']['record'] .= "<tr>";

        $querySelect = array(
            "select" => "
                SELECT
                a.employeeNo
                , CONCAT(a.lastName, ', ', a.firstName, ' ', (
                    CASE
                        WHEN ISNULL(a.middleName) THEN ''
                        ELSE CONCAT(SUBSTR(a.middleName, 1, 1), '.')
                    END
                )) AS `employeeName`
                {$surveyResponses}
            ",
            "count" => '
                SELECT COUNT(*) AS count
            '
        );
        $employeeQuery = "
            FROM employees AS a
            LEFT OUTER JOIN mscdepartment AS b ON a.FK_mscdepartment = b.PK_mscDepartment
            LEFT OUTER JOIN mscdivision AS c ON a.FK_mscdivision = c.PK_mscdivision
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

        if ($departmentId -> value != 'all' && is_numeric($departmentId -> value)) {
            $employeeQuery .= "AND b.PK_mscDepartment = '{$departmentId -> value}'";
        }

        if ($divisionId -> value != 'all' && is_numeric($divisionId -> value)) {
            $employeeQuery .= "AND c.PK_mscDivision = '{$divisionId -> value}'";
        }
        
        $executionQuery = $querySelect['count'] . $employeeQuery;
        $employeeResult = $connection -> query($executionQuery);
        $employeeRow = $employeeResult -> fetch_all(MYSQLI_ASSOC);
        $response['content']['total'] = $employeeRow[0]['count'];
        
        $executionQuery = $querySelect['select'] . $employeeQuery;
        die($executionQuery);
        $offset = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
        $executionQuery .= "LIMIT {$pageLimit -> value} OFFSET {$offset}";
        $employeeResult = $connection -> query($executionQuery);
        $employeeRecords = $employeeResult -> fetch_all(MYSQLI_ASSOC);

        $response['content']['record'] = '';

        // die($employeeQuery);

        // Table Header
        $startDateObj = new DateTime(date('Y-m-d', strtotime($sessionStartDate -> value)));
        $endDateObj = new DateTime(date('Y-m-d', strtotime($sessionEndDate -> value)));
        $noOfResponseDays = $startDateObj -> diff($endDateObj);
        $noOfResponseDays = (intval($noOfResponseDays -> format('%a')) + 1);
        $response['content']['record'] .= '
            <thead>
            <tr>
                <th rowspan="2" style="vertical-align:middle">Employee ID</th>
                <th rowspan="2" style="vertical-align:middle">Employe Name</th>
                <th colspan="' . $noOfResponseDays . '" class="text-center">Response Summary</th>
            </tr>
        ';
        $dateFlag = new DateTime(date('Y-m-d', strtotime($sessionStartDate -> value)));
        do {
            $response['content']['record'] .= '<th class="text-center">' . $dateFlag -> format('Y-m-d') . '</th>';
            $dateFlag -> add(new DateInterval('P1D'));
        } while (strtotime($dateFlag -> format('Y-m-d')) <= strtotime($sessionEndDate -> value));

        $response['content']['record'] .= '
            </thead>
            <tbody class="">
        ';
        if (count($employeeRecords) > 0) {
            foreach ($employeeRecords as $employeeRecord) {
                $response['content']['record'] .= "<tr>";
                $index = 0;
                foreach ($employeeRecord as $column) {
                    $class = (($index++) > 1) ? 'text-center' : '';
                    $response['content']['record'] .= "<td class='{$class}'>{$column}</td>";
                }
                $response['content']['record'] .= "<tr>";
            }
        } else {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] .= '<tr><td class="text-center" colspan="5">No Record Found</td></tr>';
        }
        $response['content']['record'] .= '</tbody>';
    } else {
        $errorMessage = '';
        if ($questionMstrId -> valid == 0) {
            $errorMessage = $questionMstrId -> err_msg;
        } else if ($employeeName -> valid == 0) {
            $errorMessage = $employeeName -> err_msg;
        } else if ($sessionStartDate -> valid == 0) {
            $errorMessage = $sessionStartDate -> err_msg;
        } else if ($sessionEndDate -> valid == 0) {
            $errorMessage = $sessionStartDate -> err_msg;
        } else if ($departmentId -> valid == 0) {
            $errorMessage = $departmentId -> err_msg;
        } else if ($divisionId -> valid == 0) {
            $errorMessage = $divisionId -> err_msg;
        } else if ($pageLimit -> valid == 0) {
            $errorMessage = $pageLimit -> err_msg;
        } else if ($currentPage -> valid == 0) {
            $errorMessage = $currentPage -> err_msg;
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