<?php

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
    isset($_POST['divisionName']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $divisionName = new form_validation($_POST['divisionName'], 'str-int', 'Division Name', false);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if ($divisionName -> valid == 1 && $pageLimit -> valid == 1 && $currentPage -> valid == 1) {
        $division = MscDivision::searchByName($divisionName -> value);
        if (is_array($division)) {
            $response['content']['total'] = count($division);
            if (count($division) > 0) {
                $startVal = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
                $limit = ($pageLimit -> value > count($division)) 
                    ? count($division)
                    : ((intval($currentPage -> value)) * $pageLimit -> value) - 1;
                for ($i = $startVal ; $i < $limit ; $i++) {
                    if (!isset($division[$i])) {
                        continue;
                    }
                    // Data Management Field
                    $dataManagementBtn = "
                    <button class='btn btn-success transaction-btn' title='Edit Division'
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/division-select.php'
                        data-content='{
                            &quot;divisionId&quot; : &quot;{$division[$i]['PK_mscdivision']}&quot;
                        }'
                    ><i class='fas fa-pencil-alt'></i></button>
                    <button class='btn btn-danger transaction-btn' title='Delete Division'
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/generic-warning-modal.php'
                        data-content='{
                            &quot;transType&quot;   : &quot;delete&quot;,
                            &quot;link&quot;        : &quot;../core/ajax/division-delete.php&quot;,
                            &quot;dataContent&quot; : {
                                &quot;recordId&quot;  : &quot;{$division[$i]['PK_mscdivision']}&quot;
                            },
                            &quot;headerTitle&quot; : &quot;Division&quot;
                        }'
                    ><i class='fa fa-trash'></i></button>
                    ";

                    $recipientManagementBtn = "
                        <form action='respondents.php?pageName=recipient&type=division' method='POST'>
                            <input type='text' name='recordId' hidden='hidden' value='{$division[$i]['PK_mscdivision']}'>
                            <button class='btn btn-success' title='Notification Recipients' type='submit'><i class='fas fa-eye'></i></button>
                        </form>
                    ";
                    $response['content']['record'] .= "<tr>";
                    $response['content']['record'] .= "
                        <td>{$division[$i]['PK_mscdivision']}</td>
                        <td>{$division[$i]['description']}</td>
                        <td class='text-center'>{$recipientManagementBtn}</td>
                        <td class='text-center'>{$dataManagementBtn}</td>
                    ";
                    $response['content']['record'] .= "<tr>";
                }
            }
        }

        if (strlen($response['content']['record']) === 0) {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] = '<tr><td class="text-center" colspan="4">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($divisionName -> valid == 0) {
            $errorMessage = $divisionName -> err_msg;
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