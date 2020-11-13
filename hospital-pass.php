<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Hospital Pass</title>
    <link rel="stylesheet" href="core/css/pdf-template-style.css">
    <style>
        body {
            width: 100%;
            max-width: 8.5in;
            margin: 0 auto;
            margin-top: 20px;
            padding-top: 20px;
        }

        .btn-container {
            width: 100%;
            text-align: center;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .btn-info {
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .btn-info:hover {
            color: #fff;
            background-color: #138496;
            border-color: #117a8b;
        }
        button.close-pass {
            margin: auto;
            margin-top: 25px;            
        }
    </style>
</head>
<body>
<?php

require 'core/php/_autoload.php';
require 'core/model/_autoload.php';

if (
    isset($_POST['sessionId']) && 
    isset($_POST['employeeId'])
) {
    $sessionId = new form_validation($_POST['sessionId'], 'int', 'Session ID', true);
    $employeeId = new form_validation($_POST['employeeId'], 'int', 'Employee ID', true);

    if ($sessionId -> valid == 1 && $employeeId -> valid == 1) {
        // Is Generate Hospital Pass Feature is Enabled
        $hospitalPassMessage = '';
        $hospitalPassMailingStatus = SystemFeatures::isFeatureEnabled('GEN_HOSP_PASS');
        
        if ($hospitalPassMailingStatus == true)  {
            // Generate Gate Pass PDF
            $employeeNo = Employee::show($employeeId -> value);
            // Day of the Week
            $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            // Employee Name
            $middleInitial = (!is_null($employeeNo['middleName']) || !empty($employeeNo['middleName']))
                ? substr($employeeNo['middleName'], 0, 1) . '.'
                : '';
            $employeeName = $employeeNo['firstName'] . ' ' . $middleInitial .  ' ' . $employeeNo['lastName'];
            // Department
            $department = MscDepartment::show($employeeNo['FK_mscDepartment']);
            $department = $department['description'];
            // Division
            $division = MscDivision::show($employeeNo['FK_mscDivision']);
            $division = $division['description'];

            // Health Status
            $healthStatus = QuestionSession::show($sessionId -> value);
            $color = 'green';
            if (intval($healthStatus[0]['totalRate']) > 0) $color = 'red';
            $healthStatus = "<span style='color:{$color}'>{$healthStatus[0]['remarks']}</span>";

            $validityDate = new DateTime(date('Y-m-d'));
            $validityDate -> add(new DateInterval('P1D'));

            echo '
                <div class="row">
                    <div class="col-6 text-left">
                        
                    </div>
                </div>
                <div class="row header">
                    <div class="col-12 logo-container text-center">
                        <img src="core/img/ollh-logo.gif" alt="ollh-logo" style="width: 8%;">
                    </div>
                    <div class="col-12 header-text text-center">
                        Our Lady of Lourdes Hospital <br>
                        eTriage Hospital Pass
                    </div>
                </div>

                <div class="row text-center header">
                    <div class="col-12 header-text margin-top">Health Declaration Status : </div>
                    <div class="col-12">
                        <h2 class="uppercase health-status first">
                            ' . $healthStatus . '<br>
                            <small>Valid on:</small> <small style="color: blue">' . $validityDate -> format('l') . $validityDate -> format(', F d, Y') . '</small>
                        </h2>
                        <h2 class="health-status"><b>' . $employeeNo['employeeNo'] . ' &minus; ' . $employeeName . '</b></h2>
                    </div>
                    <div class="col-12">' . $department . '</div>
                    <div class="col-12">' . $division . '</div>
                </div>

                <p class="help-text text-center">For inquiries and concerns please email us at compliance@ollh.ph</p>
                <p class="help-text text-center"><b>Hospital Pass # :</b> ' . date('Ymd') . '-' . $employeeNo['employeeNo'] . '</p>
            ';
        } else {
            echo '
                <script>
                    window.close();
                </script>
            ';
        }
        
    } else {
        echo '
            <script>
                window.close();
            </script>
        ';
    }

} else {
    echo '
        <script>
            window.location = "/";
        </script>
    ';
}

?>
<div class="btn-container">
    <button class='btn btn-info close-pass'>
        Close Hospital Pass
    </button>
</div>

<script>
    let closeBtn = document.querySelector('.close-pass');
    closeBtn.addEventListener('click', function() {
        window.close();
    });
</script>
</body>
</html>