<?php
    require_once 'includes/_autoload.php';

    $surveyId = '1';
    if (isset($_GET['surveyId']) && !empty($_GET['surveyId'])) {
        $surveyId = $_GET['surveyId'];
    }
    // Survey Details
    $survey = QuestionMstr::show($surveyId);

    if (is_null($survey)) {
        header("Location: error.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/head-tags.php'; ?>
<link rel="stylesheet" href="core/css/date-picker-modal-1.0.0.css">
<style>
    body {
        background-color: #F0F0F0;
    }

    .main-content {
        background-color: #FFF;
        padding: 10px 20px;
        min-height: 100vh
    }

    h1 {
        font-size: 30px;
        line-height: 1;
    }
    h1 small {
        font-size: 50%;
    }
    h1 .header-logo {
        width: 60px;
    }

    .loading-cover {
        background-color: #0009;
        position: fixed;
        width: 100%;
        height: 100vh;
        z-index: 10;
        display: none;
        text-align: center;
        color: #FFF;
        padding-top: 30vh;
    }

    .loading-cover .loading-icon {
        -webkit-animation: rotation 1s infinite linear;
        font-size: 140px;
    }
    .loading-cover .loading-text {
        font-size: 40px;
    }
    .show {
        display: block !important;
    }

    @-webkit-keyframes rotation {
            0% {
                -webkit-transform: rotate(0deg);
            }

            40% {
                -webkit-transform: rotate(90deg);
            }

            60% {
                -webkit-transform: rotate(270deg);
            }

            100% {
                -webkit-transform: rotate(359deg);
            }
    }
</style>
</head>
<body>
    <div class="loading-cover">
    <i class="fas fa-circle-notch loading-icon"></i> 
    <br>
    <div class="loading-text">Processing ...</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 main-content">
                <h1 class="text-center">
                    <img src="core/img/ollh-logo.gif" class='header-logo' alt="OLLH Logo"> <br>
                    <small>Our Lady of Lourdes Hospital</small> <br>
                    <?= $survey['title'] ?>
                </h1>
                <div class="dynamic-content">
                    <div class="text-justify margin-top-sm">
                    <?= $survey['description'] ?>
                    </div>
                    <div class="row">
                        <div class="col-6 offset-3 text-center margin-top-xs">
                        <button class="btn btn-info w-100 transaction-button"
                            tran-type="async-form"
                            tran-link="core/ajax/session-signin-select.php"
                            tran-data="{
                                &quot;questionMstrId&quot; : &quot;<?= $survey['PK_questionMstr'] ?>&quot;

                            }"
                            tran-container="dynamic-content"
                        >Start Survey</button>
                        </div>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
    <?php require_once 'includes/date-picker-modal.php'; ?>

    <!-- Generation of Hospital Pass Form -->
    <form method="post" action="hospital-pass.php" id="hospital-pass-form" style="display:none" target="_blank">
        <input type="text" name="employeeId" hidden>
        <input type="text" name="sessionId" hidden>
    </form>
</body>
<?php require_once 'includes/js-init.php'; ?>
<script src="core/js/date-picker-modal-1.0.0.js"></script>

<script>
    $(document).ready(function() {
        let surveyBtn = document.querySelector('.transaction-button');
        let transactionData = JSON.parse(surveyBtn.getAttribute('tran-data'));

        if (navigator.vendor.toLowerCase().indexOf('google') > -1) {
            transactionData['browser'] = 'chrome';
        } else if (navigator.vendor.toLowerCase().indexOf('apple') > -1) {
            transactionData['browser'] = 'safari';
        } else if (navigator.userAgent.toLowerCase().indexOf('mozilla') > -1) {
            transactionData['browser'] = 'firefox';
        }

        surveyBtn.setAttribute('tran-data', JSON.stringify(transactionData));

        $(document).on('click', '.transaction-button', function() {
            let type = $(this).attr("tran-type");
            let link = $(this).attr("tran-link");
            let data = JSON.parse($(this).attr("tran-data"));
            let container = "." + $(this).attr("tran-container");

            console.log(data);

            send_request_asycn(link, 'POST', data, container, type);
        });

         // Hospital Pass Generation Event
        $(document).on('click', '.gen-hosp-pass-btn', function(){
            let hospitalPassForm = document.querySelector('#hospital-pass-form');
            let hospitalPassFormInputs = hospitalPassForm.querySelectorAll('input');
            let inputValues = JSON.parse($(this).attr('tran-data'));
            hospitalPassFormInputs[0].value = inputValues['employeeId'];
            hospitalPassFormInputs[1].value = inputValues['sessionId'];

            hospitalPassForm.submit();
        });
    });
</script>
</html>