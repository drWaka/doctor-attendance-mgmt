<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

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
            // 'Registry No',
            // 'Registry Date',
            // 'Registry Type',
            'Tracking #',
            'Registration Date',
            'Patient Name',
            'Company',
            'Birthdate',
            'Testing Site',
            // 'Item Code',
            // 'Item Description',
            'IGM', 
            'IGG'
        );
        foreach ($PatientOrder AS $row) {
            if (file_exists("z:/{$row['LabNo_HCLab']}.pdf") || true) {
                // Get Examination Result from HCLab
                $examinationResult = LaboratoryExamination::getExamResult($row['LabNo_HCLab'], ['COV19M' ,'COV19G']);
                $examinationResultTemplate = array(
                    "IGG" => '',
                    "IGM" => '',
                    "rawIGG" => '',
                    "rawIGM" => ''
                );

                foreach($examinationResult AS $examRow) {
                    $arrayKey = 'IGG';
                    if ($examRow['OD_TESTCODE'] == 'COV19M') {
                        $arrayKey = 'IGM';
                    }
                    
                    $examinationResultTemplate['raw' . $arrayKey] = $examRow['OD_TR_VAL'];
                    $examinationResultTemplate[$arrayKey] = '';
                    if (!empty($examRow['OD_TR_VAL'])) {
                        if (
                            strtolower($examRow['OD_TR_VAL']) == strtolower('NEG') ||
                            strtolower($examRow['OD_TR_VAL']) == strtolower('NEGA') ||
                            strtolower($examRow['OD_TR_VAL']) == strtolower('NEGATIVE')
                        ) {
                            $examinationResultTemplate[$arrayKey] = 'Negative';
                        } else if (
                            strtolower($examRow['OD_TR_VAL']) == strtolower('POS') ||
                            strtolower($examRow['OD_TR_VAL']) == strtolower('POSI') ||
                            strtolower($examRow['OD_TR_VAL']) == strtolower('POSITIVE')
                        ) {
                            $examinationResultTemplate[$arrayKey] = 'Positive';
                        }
                    }
                }

                // Special Case for PLDT
                $testingSite = '';
                if (!empty($examinationResultTemplate['IGG']) && !empty($examinationResultTemplate['IGM'])) {
                    if ($row['guarantorId'] == '1540') {
                        $testingSite = 'PLDT STA. ANA WAREHOUSE';
                    }
                }

                $examinationReport[count($examinationReport)] = array(
                    // $row['RegistryNo'],
                    // $row['RegistryDate'],
                    // $row['RegistryType'],
                    $row['trackingNo'],
                    $row['RegistryDate'],
                    utf8_decode($row['PatientName']),
                    $row['guarantor'],
                    $row['birthDate'],
                    $testingSite,
                    // $row['ItemCode'],
                    // $row['ItemDescription'],
                    // $examinationResultTemplate['rawIGM'],
                    $examinationResultTemplate['IGM'],
                    // $examinationResultTemplate['rawIGG'],
                    $examinationResultTemplate['IGG']
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
        echo 'Examination Summary Report has been successfully';
    } else {
        die("Error Details: Error in submitted form");
    }
} else {
    die("Error Details: Insufficient Data Submitted");
}