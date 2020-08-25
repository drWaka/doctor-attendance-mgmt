<?php
    $recordType = $recordId = '';
    if (isset($_POST['recordId']) && isset($_GET['type'])) {
        $recordType = $_GET['type'];
        $recordId = $_POST['recordId'];
    } else {
        // Redirect JS Script
        echo "
            <script>
                window.location.href='homepage.php';
            </script>
        ";
    }
?>

<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Notification Recipient Management
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-7">
            <input type="text" name="employeeName" class="form-control filter-search" placeholder="Recipient Name" />
        </div>

    </div>
    </div>
</div>

<input type="text" name="recordId" value="<?= $recordId ?>" hidden="hidden"/>
<input type="text" name="recordType" value="<?= $recordType ?>" hidden="hidden"/>

<div class="row margin-bottom">
    <div class="col-12">
    <table class="table table-hover table-dashed">
        <thead>
        <tr>
            <th>Employee Name</th>
            <th>Email Address</th>
            <th class='text-center'>Manage</th>
        </tr>
        </thead>
        <tbody class="record-container">
        
        </tbody>
    </table>
    </div>
    <div class="col-4 offset-8">
        <div class="row text-center pagination-container">
            <div class="col-3 text-right">
                <button class="btn nav-btn btn-light prev-btn" data-container="record-container"><span class="fas fa-chevron-left"></span></button>
            </div>
            <div class="col-6">
                <button class="btn btn-info w-100 transaction-btn"
                    title='Add New Recipient'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/recipient-select.php'
                    data-content='{
                        &quot;referenceRecordId&quot; : &quot;<?=$recordId?>&quot;,
                        &quot;recordType&quot; : &quot;<?= $recordType ?>&quot;
                    }'
                >Add Recipient</button>
            </div>
            <div class="col-3 text-left">
                <button class="btn nav-btn btn-light next-btn" data-container="record-container"><span class="fas fa-chevron-right"></span></button>
            </div>
        </div>
    </div>
</div>

</div>

<script>

    function loadRecord() {
        let employeeName = document.querySelector('[name="employeeName"]').value;
        let recordId = document.querySelector('[name="recordId"]').value;
        let recordType = document.querySelector('[name="recordType"]').value;
        send_request_asycn (
          '../core/ajax/recipient-content.php', 
          'POST', 
          {
            employeeName : employeeName, 
            recordId : recordId, 
            recordType : recordType, 
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }
</script>