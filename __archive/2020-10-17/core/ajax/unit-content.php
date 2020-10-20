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
    isset($_POST['unitName']) &&
    isset($_POST['departmentId']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $unitName = new form_validation($_POST['unitName'], 'str-int', 'Unit Name', false);
    $departmentId = new form_validation($_POST['departmentId'], 'str-int', 'Department ID', true);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if ($unitName -> valid == 1 && $departmentId -> valid == 1 && $pageLimit -> valid == 1 && $currentPage -> valid == 1) {
        // Validate Division Record
        if ($departmentId -> value !== 'all') {
            $department = MscDepartment::show($departmentId -> value);

            $departmentId -> valid = 1;
            $departmentId -> err_msg = 'Department record not found';
            if (is_array($department)) {
                if (count($department) > 0) {

                }
            }
        }
    }

    if ($unitName -> valid == 1 && $departmentId -> valid == 1 && $pageLimit -> valid == 1 && $currentPage -> valid == 1) {
        $filter = array(
            "description" => $unitName -> value
        );
        if ($departmentId -> value !== 'all') $filter['departmentId'] = $departmentId -> value;

        $unit = MscUnit::filter($filter);
        if (is_array($unit)) {
            $response['content']['total'] = count($unit);
            if (count($unit) > 0) {
                $startVal = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
                $limit = ($pageLimit -> value > count($unit)) 
                    ? count($unit)
                    : ((intval($currentPage -> value)) * $pageLimit -> value) - 1;

                for ($i = $startVal ; $i < $limit ; $i++) {
                    if (!isset($unit[$i])) {
                        continue;
                    }
                    // Data Management Field
                    $dataManagementBtn = "
                    <button class='btn btn-success transaction-btn' title='Edit Department'
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/unit-select.php'
                        data-content='{
                            &quot;unitId&quot; : &quot;{$unit[$i]['PK_mscUnit']}&quot;
                        }'
                    ><i class='fas fa-pencil-alt'></i></button>
                    <button class='btn btn-danger transaction-btn' title='Delete Department'
                        trans-name='async-form'
                        data-target='.modal-container'
                        data-link='../core/ajax/generic-warning-modal.php'
                        data-content='{
                            &quot;transType&quot;   : &quot;delete&quot;,
                            &quot;link&quot;        : &quot;../core/ajax/unit-delete.php&quot;,
                            &quot;dataContent&quot; : {
                                &quot;recordId&quot;  : &quot;{$unit[$i]['PK_mscUnit']}&quot;
                            },
                            &quot;headerTitle&quot; : &quot;Unit&quot;
                        }'
                    ><i class='fa fa-trash'></i></button>
                    ";

                    $recipientManagementBtn = "
                        <form action='respondents.php?pageName=recipient&type=unit' method='POST'>
                            <input type='text' name='recordId' hidden='hidden' value='{$unit[$i]['PK_mscUnit']}'>
                            <button class='btn btn-success' title='Notification Recipients' type='submit'><i class='fas fa-eye'></i></button>
                        </form>
                    ";
                    $response['content']['record'] .= "<tr>";
                    $response['content']['record'] .= "
                        <td>{$unit[$i]['PK_mscUnit']}</td>
                        <td>{$unit[$i]['unit']}</td>
                        <td>{$unit[$i]['department']}</td>
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
        if ($unitName -> valid == 0) {
            $errorMessage = $unitName -> err_msg;
        } else if ($departmentId -> valid == 0) {
            $errorMessage = $departmentId -> err_msg;
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