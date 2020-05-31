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
</style>
</head>
<body>    
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
                            tran-data="{&quot;questionMstrId&quot; : &quot;<?= $survey['PK_questionMstr'] ?>&quot;}"
                            tran-container="dynamic-content"
                        >Start Survey</button>
                        </div>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
</body>
<?php require_once 'includes/js-init.php'; ?>

<script>
    $(document).ready(function() {
        
        $(document).on('click', '.transaction-button', function() {
            let type = $(this).attr("tran-type");
            let link = $(this).attr("tran-link");
            let data = JSON.parse($(this).attr("tran-data"));
            let container = "." + $(this).attr("tran-container");

            console.log(data);

            send_request_asycn(link, 'POST', data, container, type);
        });
    });
</script>
</html>