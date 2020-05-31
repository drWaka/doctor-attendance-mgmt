<?php

require '../php/_autoload.php';

// Step #2: Update the Laboratory No of the Imported Patient charges to the HC Lab LabNo

// Get all of the Unsent Patient Order where the LabNo is NULL
$patientOrderQry = "
    SELECT a.PK_AA_PatientOrders, a.FK_TRXNO_CH
    FROM AA_PatientOrders AS a
    WHERE a.LabNo_HCLab IS NULL
        AND a.isSentFlag = 0
";
$patientOrderRes = $bizboxDB -> query($patientOrderQry);
$patientOrderRec = $patientOrderRes -> fetchAll();

if (is_array($patientOrderRec)) {
    if (count($patientOrderRec) > 0) {
        foreach($patientOrderRec as $row) {
            // Get the Equivalent LabNo from the HCLab Database
            $hcLabOrderQry = "
                SELECT a.OH_TNO
                FROM ORD_HDR a
                WHERE a.OH_ONO = '{$row['FK_TRXNO_CH']}'
            ";
            // die($hcLabOrderQry);
            $hcLabOrderRes = oci_parse($hcLabDB, $hcLabOrderQry);
            oci_execute($hcLabOrderRes);

            $labNo = '';
            while ($xrow = oci_fetch_array($hcLabOrderRes, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $labNo = $xrow['OH_TNO'];
            }

            if (!empty($labNo)) {
                // Update the LabNo Field at the PatientOrders
                $updateLabNoQry = "
                    UPDATE AA_PatientOrders
                    SET LabNo_HCLab = '{$labNo}'
                    WHERE PK_AA_PatientOrders = '{$row['PK_AA_PatientOrders']}'
                ";
                // die($updateLabNoQry);

                try {
                    $bizboxDB -> query($updateLabNoQry);
                } catch (Exception $e) {
                    PatientOrderLog::insertLog(
                        $row['PK_AA_PatientOrders']
                        , PatientOrderLog::LABNO_PULLING_LOG
                        , PatientOrderLog::FAIL_LOG
                        , "Unable to Update Patient Order Laboratory No"
                    );
                }
            }
        }
        
    }
}