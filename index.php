<?php
    require_once 'includes/_autoload.php';

    $surveyId = '1';
    if (isset($_GET['surveyId']) && !empty($_GET['surveyId'])) {
        $surveyId = $_GET['surveyId'];
    }

    // Survey Details
    $surveyMstrQry = "SELECT * FROM questionMstr WHERE PK_questionMstr = '{$surveyId}'";
    $surveyMstrRes = $connection -> query($surveyMstrQry);

    $surveyMstrRec = '';
    if ($surveyMstrRes -> num_rows > 0) {
        $surveyMstrRec = $surveyMstrRes -> fetch_object();
    } else {
        // Redirect to Error Page
        // Error Message : Survey Not Found
    }

    // die(var_dump($surveyMstrRec));
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
    }
</style>
</head>
<body>    
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-10 offset-md-2 offset-1 main-content margin-top-sm">
                <h1 class="text-center"><?= $surveyMstrRec -> title ?></h1>
                <div class="dynamic-content">
                    <div class="text-justify">
                    <?= $surveyMstrRec -> description ?>
                    </div>
                    <div class="row">
                        <div class="col-6 offset-3 text-center">
                        <button class="btn btn-info w-100 transaction-button"
                            tran-type="async-form"
                            tran-link="core/ajax/session-signin-select.php"
                            tran-data="{&quot;questionMstrId&quot; : &quot;<?= $surveyId ?>&quot;}"
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