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
    isset($_POST['departmentName']) &&
    isset($_POST['divisionId']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $departmentName = new form_validation($_POST['departmentName'], 'str-int', 'Department Name', false);
    $divisionId = new form_validation($_POST['divisionId'], 'str-int', 'Division ID', true);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if ($departmentName -> valid == 1 && $divisionId -> valid == 1 && $pageLimit -> valid == 1 && $currentPage -> valid == 1) {
        // Validate Division Record
        if ($divisionId -> value !== 'all') {
            $division = MscDivision::show($divisionId -> value);

            $divisionId -> valid = 1;
            $divisionId -> err_msg = 'Division record not found';
            if (is_array($division)) {
                if (count($division) > 0) {

                }
            }
        }
    }

    if ($departmentName -> valid == 1 && $divisionId -> valid == 1 && $pageLimit -> valid == 1 && $currentPage -> valid == 1) {
        $filter = array(
            "description" => $departmentName -> value
        );
        if ($divisionId -> value !== 'all') $filter['divisionId'] = $divisionId -> value;
        $department = MscDepartment::filter($filter);
        if (is_array($department)) {
            $response['content']['total'] = count($department);
            if (count($department) > 0) {
                $startVal = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
                $limit = ($pageLimit -> value > count($department)) 
                    ? count($department)
                    : ((intval($currentPage -> value)) * $pageLimit -> value) - 1;

                for ($i = $startVal ; $i < $limit ; $i++) {
                    if (!isset($department[$i])) {
                        continue;
                    }
                    // Data Management Field
                    $dataManagementBtn = "
                    <button class='btn btn-success transaction-btn' title='Edit Department'
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/department-select.php'
                        data-content='{
                            &quot;departmentId&quot; : &quot;{$department[$i]['PK_mscDepartment']}&quot;
                        }'
                    ><i class='fas fa-pencil-alt'></i></button>
                    <button class='btn btn-danger transaction-btn' title='Delete Department'
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/generic-warning-modal.php'
                        data-content='{
                            &quot;transType&quot;   : &quot;delete&quot;,
                            &quot;link&quot;        : &quot;../core/ajax/department-delete.php&quot;,
                            &quot;dataContent&quot; : {
                                &quot;recordId&quot;  : &quot;{$department[$i]['PK_mscDepartment']}&quot;
                            },
                            &quot;headerTitle&quot; : &quot;Department&quot;
                        }'
                    ><i class='fa fa-trash'></i></button>
                    ";

                    $recipientManagementBtn = "
                        <form action='respondents.php?pageName=recipient&type=department' method='POST'>
                            <input type='text' name='recordId' hidden='hidden' value='{$department[$i]['PK_mscDepartment']}'>
                            <button class='btn btn-success' title='Notification Recipients' type='submit'><i class='fas fa-eye'></i></button>
                        </form>
                    ";
                    $response['content']['record'] .= "<tr>";
                    $response['content']['record'] .= "
                        <td>{$department[$i]['PK_mscDepartment']}</td>
                        <td>{$department[$i]['department']}</td>
                        <td>{$department[$i]['division']}</td>
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
            $response['content']['record'] = '<tr><td class="text-center" colspan="5">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($departmentName -> valid == 0) {
            $errorMessage = $departmentName -> err_msg;
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