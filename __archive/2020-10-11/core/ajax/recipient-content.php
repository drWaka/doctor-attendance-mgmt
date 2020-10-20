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
    isset($_POST['recordId']) &&
    isset($_POST['recordType']) &&
    isset($_POST['employeeName']) &&
    isset($_POST['pageLimit']) &&
    isset($_POST['currentPage'])
) {
    $recordId = new form_validation($_POST['recordId'], 'int', 'Reference Record ID', true);
    $recordType = new form_validation($_POST['recordType'], 'str', 'Record Type', true);
    $employeeName = new form_validation($_POST['employeeName'], 'str-int', 'Employee Name', false);

    $pageLimit = new form_validation($_POST['pageLimit'], 'int', 'Page Limit', true);
    $currentPage = new form_validation($_POST['currentPage'], 'int', 'Page No', true);

    if (
        $recordId -> valid == 1 && $recordType -> valid == 1 && 
        $employeeName -> valid == 1 && $pageLimit -> valid == 1 && 
        $currentPage -> valid == 1
    ) {
        // Validate Record ID
        $record = [];
        if ($recordType -> value == 'division') {
            $record = MscDivision::show($recordId -> value);
        } else if ($recordType -> value == 'department') {
            $record = MscDepartment::show($recordId -> value);
        } else if ($recordType -> value == 'unit') {
            $record = MscUnit::show($recordId -> value);
        } else {
            $recordType -> valid = 0;
            $recordType -> err_msg = 'Invalid Record Type';
        }

        if (is_array($record)) {
            if (count($record) == 0) {
                $recordId -> valid = 0;
                $recordId -> err_msg = 'Invalid Record ID';
            }
        } else {
            $recordId -> valid = 0;
            $recordId -> err_msg = 'Invalid Record ID';
        }
    }

    if (
        $recordId -> valid == 1 && $recordType -> valid == 1 && 
        $employeeName -> valid == 1 && $pageLimit -> valid == 1 && 
        $currentPage -> valid == 1
    ) {
        $notifRecipients = MscEmailNotif::filter(array(
            "FK_mscRecord" => $recordId -> value,
            "recordType" => $recordType -> value,
            "employeeName" => $employeeName -> value
        ));
        if (is_array($notifRecipients)) {
            $response['content']['total'] = count($notifRecipients);
            if (count($notifRecipients) > 0) {
                $startVal = ((intval($currentPage -> value) - 1) * $pageLimit -> value);
                $limit = ($pageLimit -> value > count($notifRecipients)) 
                    ? count($notifRecipients)
                    : ((intval($currentPage -> value)) * $pageLimit -> value) - 1;
                for ($i = $startVal ; $i < $limit ; $i++) {
                    if (!isset($notifRecipients[$i])) {
                        continue;
                    }
                    // Data Management Field
                    $dataManagementBtn = "
                        <button class='btn btn-danger transaction-btn' title='Delete Recipient'
                            trans-name='async-form'
                            data-target='.modal-container'
                            data-link='../core/ajax/generic-warning-modal.php'
                            data-content='{
                                &quot;transType&quot;   : &quot;delete&quot;,
                                &quot;link&quot;        : &quot;../core/ajax/recipient-delete.php&quot;,
                                &quot;dataContent&quot; : {
                                    &quot;recordId&quot;  : &quot;{$notifRecipients[$i]['PK_mscEmailNotif']}&quot;
                                },
                                &quot;headerTitle&quot; : &quot;Notification Reciepient&quot;
                            }'
                        ><i class='fa fa-trash'></i></button>
                    ";

                    $response['content']['record'] .= "<tr>";
                    $response['content']['record'] .= "
                        <td>{$notifRecipients[$i]['employeeName']}</td>
                        <td>{$notifRecipients[$i]['email']}</td>
                        <td class='text-center'>{$dataManagementBtn}</td>
                    ";
                    $response['content']['record'] .= "<tr>";
                }
            }
        }

        if (strlen($response['content']['record']) === 0) {
            // No Employee Record Found
            $response['content']['total'] = 1;
            $response['content']['record'] = '<tr><td class="text-center" colspan="3">No Record Found</td></tr>';
        }
    } else {
        $errorMessage = '';
        if ($recordId -> valid == 0) {
            $errorMessage = $recordId -> err_msg;
        } else  if ($recordType -> valid == 0) {
            $errorMessage = $recordType -> err_msg;
        } else  if ($employeeName -> valid == 0) {
            $errorMessage = $employeeName -> err_msg;
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