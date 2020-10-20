<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once 'includes/head-tags.php'; ?>
<style>
    body {
        background-color: #FFF;
    }
    h1 {
        font-size: 170px;
        line-height: 1;
    }
    h1 small {
        font-size: 40%;
    }
    .text-details {
        font-size: 40px;
        text-align: center;
        margin-top: 30px;
    }
    a:hover {
        text-decoration: none;
    }
</style>
</head>
<body>    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center"><small>ERROR</small><br>404</h1>
                <div class="dynamic-content">
                    <div class="col-10 offset-1 text-details">PAGE NOT FOUND</div>
                </div>
                <div class="text-center margin-top-lg">
                    <a href="index.php"><span class="fa fa-home"></span> Take be back to Homepage</a>
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