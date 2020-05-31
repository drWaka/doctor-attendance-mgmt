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

if (isset($_POST['startDate']) && isset($_POST['endDate'])) {
    $startDate = new form_validation($_POST['startDate'], 'date', 'Start Date', true);
    $endDate = new form_validation($_POST['endDate'], 'date', 'End Date', true);

    if ($startDate -> valid == 1 && $endDate -> valid == 1) {
        $examinationReport = array();
        $PatientOrder = PatientOrder::getByDate(
            array(
                "startDate" => $startDate -> value,
                "endDate" => $endDate -> value
            )
        );

        $examinationReport[count($examinationReport)] = array(
            'Registry No',
            'Registry Date',
            'Registry Type',
            'Patient Name',
            'Item Code',
            'Item Description',
            'IGG',
            'IGM'
        );
        foreach ($PatientOrder AS $row) {
            if (file_exists("z:/{$row['LabNo_HCLab']}.pdf") || true) {
                // Get Examination Result from HCLab
                $examinationResult = LaboratoryExamination::getExamResult($row['LabNo_HCLab'], ['COV19M' ,'COV19G']);
                $examinationResultTemplate = array(
                    "IGG" => '',
                    "IGM" => ''
                );
                foreach($examinationResult AS $examRow) {
                    $arrayKey = 'IGG';
                    if ($examRow['OD_TESTCODE'] == 'COV19M') {
                        $arrayKey = 'IGM';
                    }

                    $examinationResultTemplate[$arrayKey] = 'Positive';
                    if ($examRow['OD_TR_VAL'] == 'NEGA') {
                        $examinationResultTemplate[$arrayKey] = 'Negative';
                    }
                }

                $examinationReport[count($examinationReport)] = array(
                    $row['RegistryNo'],
                    $row['RegistryDate'],
                    $row['RegistryType'],
                    $row['PatientName'],
                    $row['ItemCode'],
                    $row['ItemDescription'],
                    $examinationResultTemplate['IGG'],
                    $examinationResultTemplate['IGM'],
                );
            }
        }

        $currentTimeStamp = strval(strtotime(date("Y-m-d h:i:s")));
        // die(date("Y-m-d H:i:s"));
        $filePath = "../files/csv/{$currentTimeStamp}.csv";
        $csvReport = fopen($filePath, 'w');

        foreach ($examinationReport as $row) {
            fputcsv($csvReport, $row);
        }

        fclose($csvReport);

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

        // ToDo : Research on how to validate whether the file is already downloaded

        $response['contentType'] = 'modal';
        $response['content']['modal'] = modalize(
            '<div class="row text-center">
                <h2 class="header capitalize col-12">Download Success</h2>
                <p class="para-text col-12">Examination Summary Report has been successfully downloaded</p>
            </div>', 
            array(
                "trasnType" => 'error',
                "btnLbl" => 'Dismiss'
            )
        );   
    } else {
        $startDateErr = new error_handler($startDate -> err_msg);
        $endDateErr = new error_handler($endDate -> err_msg);

        $response['contentType'] = 'dynamic-content';
        $response['content']['form'] = "
            <div class='form-row'>
                <label for='col-12'>Start Date: </label>
                <div class='col-12'>
                    <input type='date' name='startDate' class='form-control {$startDateErr -> error_class}' value='{$startDate -> value}'>
                    {$startDateErr -> error_icon}
                    {$startDateErr -> error_text}
                </div>
            </div>
            <div class='form-row'>
                <label for='col-12'>End Date: </label>
                <div class='col-12'>
                    <input type='date' name='endDate' class='form-control {$endDateErr -> error_class}' value='{$endDate -> value}'>
                    {$endDateErr -> error_icon}
                    {$endDateErr -> error_text}
                </div>
            </div>
            <div class='row'>
                <div class='form-group col-12'>
                    <button type='submit' class='btn btn-success w-100 submit-btn'>Generate CSV</button>
                </div>
            </div>  
        ";
    }
} else {
    $response['contentType'] = 'modal';
    $response['content']['modal'] = modalize(
        '<div class="row text-center">
            <h2 class="header capitalize col-12">Error Encountered</h2>
            <p class="para-text col-12">Error Details: Insufficient Data Submitted</p>
        </div>', 
        array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
        )
    );   
}

// Encode JSON Response
encode_json_file($response);