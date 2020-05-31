<?php

require '../php/_autoload.php';
require '../templates/patient-result-template.php';

// Select All Pending Records
$pendingResultQry = "
  SELECT 
    PK_AA_PatientOrders 
    , LabNo_HCLab
  FROM AA_PatientOrders
  WHERE isSentFlag <> 1
    AND LabNo_HCLab IS NOT NULL
    AND isCNFlag <> 1
    AND PK_AA_PatientOrders = '584'
";
$pendingResultRes = $bizboxDB -> query($pendingResultQry);
$pendingResultRec = $pendingResultRes -> fetchAll();

if (is_array($pendingResultRec)) {
  if (count($pendingResultRec) > 0) {
    foreach ($pendingResultRec as $row) {
      // Check if PDF Result File Exists
      $fileDir = 'Z:/' . $row['LabNo_HCLab'] . '.pdf';
      // die(var_dump(file_exists($fileDir)));
      if (file_exists($fileDir)) {
        // Email Result Reciever(s)
        $recieverInfo = array(
          "receiver" => [],
          "copy" => []
        );

        // Get the Patient email address
        $patientContactQry = "
          SELECT c.fullname, RTRIM(LTRIM(c.email)) 
          FROM AA_PatientOrders as a
          INNER JOIN psPatRegisters AS b ON a.FK_psPatRegisters = b.PK_psPatRegisters
          INNER JOIN psDataCenter AS c ON (b.FK_emdPatients = c.PK_psDataCenter AND a.FK_emdPatients = c.PK_psDataCenter)
          WHERE a.PK_AA_PatientOrders = '{$row['PK_AA_PatientOrders']}'
        ";
        // die($patientContactQry);
        $patientContactRes = $bizboxDB -> query($patientContactQry);
        $patientContactRec = $patientContactRes -> fetchAll();

        if (is_array($patientContactRec)) {
          if (count($patientContactRec) > 0) {
            if (!empty($patientContactRec[0]['email'])){
              // validate if the email is valid
              if (
                !empty($patientContactRec[0]['email']) 
                && filter_var($patientContactRec[0]['email'], FILTER_VALIDATE_EMAIL)
              ) {
                $recieverInfo['receiver'][0] = new stdClass();
                $recieverInfo['receiver'][0] -> email = $patientContactRec[0]['email'];
                $recieverInfo['receiver'][0] -> fullname = $patientContactRec[0]['fullname'];
              }
            } else {
              PatientOrderLog::insertLog(
                $row['PK_AA_PatientOrders']
                , PatientOrderLog::EMAIL_SENDING_LOG
                , PatientOrderLog::FAIL_LOG
                , "Patient Doesn't have a valid email address"
              );
            }
          }
        }

        // Get the Guarrantor/Company Email Address
        // Special Case for PLDT
        $guarrantorContactQry = "
          SELECT d.email, d.fullname
          FROM AA_PatientOrders AS a
          INNER JOIN psPatRegisters AS b ON a.FK_psPatRegisters = b.PK_psPatRegisters
          INNER JOIN psGntrLedgers AS c ON (c.FK_psPatRegisters = b.PK_psPatRegisters AND a.FK_psPatRegisters = c.FK_psPatRegisters)
          INNER JOIN psDataCenter AS d ON c.FK_faCustomers = d.PK_psDataCenter
          WHERE a.PK_AA_PatientOrders = '{$row['PK_AA_PatientOrders']}'
            AND d.PK_psDataCenter = '1540' --Special Purpose for PLDT
        ";
        $guarrantorContactRes = $bizboxDB -> query($guarrantorContactQry);
        $guarrantorContactRec = $guarrantorContactRes -> fetchAll();

        if (is_array($guarrantorContactRec)) {
          if (count($guarrantorContactRec) > 0) {
            // PLDT Doctors
            // $recieverInfo['copy'][0] = new stdClass();
            // $recieverInfo['copy'][0] -> email = 'rmbejar@pldt.com.ph';
            // $recieverInfo['copy'][0] -> fullname = 'Dr. Rafael Bejar';

            // $recieverInfo['copy'][1] = new stdClass();
            // $recieverInfo['copy'][1] -> email = 'ovbernardo@pldt.com.ph';
            // $recieverInfo['copy'][1] -> fullname = 'Dr. Orven Bernardo';

            // $recieverInfo['copy'][2] = new stdClass();
            // $recieverInfo['copy'][2] -> email = 'rmlimjoco@pldt.com.ph';
            // $recieverInfo['copy'][2] -> fullname = 'Dr. Rafael Limjoco, Jr.';

            $recieverInfo['copy'][0] = new stdClass();
            $recieverInfo['copy'][0] -> email = 'renzopangyarihan@gmail.com';
            $recieverInfo['copy'][0] -> fullname = 'Renzo Pangyarihan';

            $recieverInfo['copy'][1] = new stdClass();
            $recieverInfo['copy'][1] -> email = 'rpangyarihan@ollh.ph';
            $recieverInfo['copy'][1] -> fullname = 'Renzo Pangyarihan';

            $recieverInfo['copy'][2] = new stdClass();
            $recieverInfo['copy'][2] -> email = 'suarez.yancy@gmail.com';
            $recieverInfo['copy'][2] -> fullname = 'Yancy Suarez';


            // if (!empty('')){
            //   // validate if the email is valid
            //   if (
            //     !empty($guarrantorContactRec[0]['email']) 
            //     && filter_var($guarrantorContactRec[0]['email'], FILTER_VALIDATE_EMAIL)
            //   ) {
            //     // $recieverInfo['copy'][0] = new stdClass();
            //     // $recieverInfo['copy'][0] -> email = $guarrantorContactRec[0]['email'];
            //     // $recieverInfo['copy'][0] -> fullname = $guarrantorContactRec[0]['fullname'];

                
            //   }
            // } else {
            //   PatientOrderLog::insertLog(
            //     $row['PK_AA_PatientOrders']
            //     , PatientOrderLog::EMAIL_SENDING_LOG
            //     , PatientOrderLog::FAIL_LOG
            //     , "Employeer/Guarrantor Doesn't have a valid email address"
            //   );
            // }
          }
        }

        // Check if the patient & company has a valid email
        if (count($recieverInfo['receiver']) > 0) {
          // Email Message Initialization
          $emailContent = new stdClass();
          $emailContent -> title = "Laboratory report for {$recieverInfo['receiver'][0]->fullname}";
          $emailContent -> mainBody = "
            <div style='width : 100%; background-color : #F0F0F0; padding-top: 20px; padding-bottom: 20px; text-align : center; font-family: Arial, sans-serif;'>
              <div style='width : 80%; text-align : left; background-color : #FFF; margin : auto; padding: 15px;'>
                
                Dear {$recieverInfo['receiver'][0]->fullname} , <br/><br>
                Please see the attached file below for the result of your laboratory test(s). <br/>
                Thank you for choosing us for your laboratory needs. <br/>
              
                
                <br><br><br>

                Sincerely yours,<br><br>
                OUR LADY OF LOURDES HOSPITAL<br>
                Department of Pathology and Laboratory Medicine<br>

              </div>
            </div>
          ";
          $emailContent -> alternateBody = "
              Dear {$recieverInfo['receiver'][0]->fullname} , <br/><br>
              Please see the attached file below for the result of your laboratory test(s). <br/>
              Thank you for choosing us for your laboratory needs. <br/>
              
              <br/>

              Sincerely yours,<br><br>
              OUR LADY OF LOURDES HOSPITAL
              Department of Pathology and Laboratory Medicine<br>
          ";
          $emailContent -> attachments = array(
            array(
              "path" => $fileDir,
              "fileName" => 'examination-no-' . $row['LabNo_HCLab'] . '.pdf'
            )
          );

          // Invoke Send Email Function
          $result = sendEmail($recieverInfo, $emailContent);

          die(var_dump($result));
          if ($result) {
            $updateIsSendFlag = "
              UPDATE AA_PatientOrders
              SET isSentFlag = 1,
                  sentDate = CURRENT_TIMESTAMP
              WHERE PK_AA_PatientOrders = '{$row['PK_AA_PatientOrders']}'
            " ;
            // die($updateIsSendFlag);

            try {
              $bizboxDB -> query($updateIsSendFlag);
            } catch (Exception $e) {
              PatientOrderLog::insertLog(
                $row['PK_AA_PatientOrders']
                , PatientOrderLog::EMAIL_SENDING_LOG
                , PatientOrderLog::FAIL_LOG
                , "Unable to Update IsSent Column"
              );
            }
          }
        } else {
          echo($row['PK_AA_PatientOrders'] . '<br>');
        }
      }
    }
  }
}