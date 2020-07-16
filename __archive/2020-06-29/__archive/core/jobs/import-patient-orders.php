<?php

require '../php/_autoload.php';

// Step #1: Import all of the LIS Processed Examination Charges From Bizbox
$insertPatientOrderQry = "
    INSERT INTO AA_PatientOrders (
        FK_psPatRegisters, registrydate, FK_emdPatients
        , registryno, pattrantype, FK_TRXNO_CH
        , CH_rendate, FK_iwItem
    )
    SELECT 
        a.PK_psPatRegisters
        , a.registrydate
        , a.FK_emdPatients
        , a.registryno
        , a.pattrantype
        , b.PK_TRXNO 
        , b.rendate
        , d.PK_iwItems
    FROM psPatRegisters AS a
    INNER JOIN psPatinv AS b ON a.PK_psPatRegisters = b.FK_psPatRegisters
    INNER JOIN psPatitem AS c ON (b.PK_TRXNO = c.FK_TRXNO AND b.FK_psPatRegisters = c.FK_psPatRegisters)
    INNER JOIN iwItems AS d ON d.PK_iwItems = c.FK_iwItemsREN
    WHERE d.itemgroup IN ('EXM')
        AND b.isProcessLISFlag = 1
        AND CONVERT(DATE, b.rendate) >= '2020-05-15'
        AND b.PK_TRXNO NOT IN (
            SELECT xx.FK_TRXNO_CH FROM AA_PatientOrders AS xx
        )
        AND b.doctype NOT IN ('CN')

        --Limit by Covid-19 Rapid Test Initially
        AND d.PK_iwItems = 'LAB0000476'
        
        --Testing Purposes
        --AND a.FK_emdPatients = '80070'
";

if (!$bizboxDB -> query($insertPatientOrderQry)) {
    // ToDo : Insert Error Log - Error on Importing Patient Charges
}






// // HCLab Record Test
// $stid = oci_parse($hcLabDB, "
//     SELECT * FROM ORD_HDR
//     WHERE OH_LAST_NAME LIKE '%OLLH, DUMMY%'
//     ORDER BY OH_TRX_DT DESC
//     --OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY;
// ");
// oci_execute($stid);

// // echo "<table border='1'>\n";
// while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
//     // echo "<tr>\n";
//     die(var_dump($row));
//     // foreach ($row as $item) {
//     //     echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
//     // }
//     // echo "</tr>\n";
// }
// // echo "</table>\n";
