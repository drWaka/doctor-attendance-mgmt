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
                    <div class="col-10 offset-1">Date of onset of symptoms stated previously?</div>

                    <div class='col-10 offset-1'>
                        <select name='response' class='form-control' id='>
                            <option value=''>Select Response</option>
                            <option value='yes'>Yes, I did</option>
                            <option value='no'>No, I didn't</option>
                        </select>
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