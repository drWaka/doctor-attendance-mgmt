<?php

require '../php/_autoload.php';

// Step #3 : Void all of the Credit Noted Patient Charges
$updatePatientOrderQry = "
    UPDATE a
    SET a.FK_TRXNO_CN = b.PK_TRXNO,
        a.CN_rendate = b.rendate,
        a.isCNFlag = 1
    FROM AA_PatientOrders AS a
    INNER JOIN psPatinv AS b ON (a.FK_psPatRegisters = b.FK_psPatRegisters AND a.FK_TRXNO_CH = b.FK_TRXNO_CN)
    INNER JOIN pspatitem AS c ON (b.PK_TRXNO = c.FK_TRXNO AND b.FK_psPatRegisters = c.FK_psPatRegisters)
    INNER JOIN iwItems AS d ON (c.FK_iwItemsREN = d.PK_iwItems)
    WHERE d.itemgroup IN ('EXM')
    AND CONVERT(DATE, b.rendate) >= '2020-05-15'
    AND b.doctype IN ('CN')

    --Testing Purposes
    --AND a.FK_emdPatients = '80070'
";

if (!($bizboxDB -> query($updatePatientOrderQry))) {
    // ToDo : Insert Error Log - Error on Importing Patient Charges
}