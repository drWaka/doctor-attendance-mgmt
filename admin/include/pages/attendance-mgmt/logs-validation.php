<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Logs Validation
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-4">
            <div class="input-group">
                <input type="text" name="employeeName" class="form-control" placeholder="Doctor Name / ID" />
                <div class="input-group-append">
                    <button class="btn btn-info filter-search-btn" data-target="[name='employeeName']" type="button"><i class="fa fa-search"></i></button>
                </div>
            </div> 
        </div>

        <div class="col-md-3">
        <div class="form-group">
            <button class="btn btn-info filter-toggle w-100">Toggle Filter</button>
        </div>
        </div>

    </div>
    </div>
</div>
<div class="row advance-filter hide">

    <div class="col-4 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label for="useDateRng" class="rangeLbl"> Log Date :</label>
        </div>
        <div class="col-12">
            <input type="date" name="logDate" class="form-control filter" value="<?= date('Y-m-d'); ?>">
        </div>
    </div>
    </div>

    <div class="col-4 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label for="useDateRng" class="rangeLbl"> Department :</label>
        </div>
        <div class="col-12">
            <select name="departmentId" class="form-control filter" id="">
                <option value="all">All Department</option>
                <?php
                    $departmentQry = "SELECT * FROM mscdepartment";
                    $departmentRes = $connection -> query($departmentQry);

                    if ($departmentRes -> num_rows > 0) {
                        while ($departmentRow = $departmentRes -> fetch_assoc()) {
                            echo "<option value='{$departmentRow['PK_mscdepartment']}'>{$departmentRow['description']}</option>";
                        }
                    }
                ?>
            </select>
        </div>
    </div>
    </div>
</div>


<div class="row margin-bottom">
    <div class="col-12">
    <table class="table table-hover table-dashed">
        <thead>
        <tr>
            <th>PRC ID</th>
            <th>Doctor Name</th>
            <th>Log Date</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Schedule</th>
            <th>Manage</th>
        </tr>
        </thead>
        <tbody class="record-container">
        
        </tbody>
    </table>
    </div>
    <div class="col-3">
        <form action="../core/requests/report-question-response.php" method="post" target="_blank">
            <input type="text" name="csvEmployeeName" hidden>
            <input type="text" name="csvLogDate" hidden>
            <input type="date" name="csvDepartmentId" hidden>
            <button class="btn btn-success w-100">Generate CSV File</button>
        </form>
    </div>
    <div class="col-4 offset-5">
        <div class="row text-center pagination-container">
            <div class="col-3 offset-6 text-right">
            <button class="btn nav-btn btn-light prev-btn" data-container="record-container"><span class="fas fa-chevron-left"></span></button>
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
        let logDate = document.querySelector('[name="logDate"]').value;
        let departmentId = document.querySelector('[name="departmentId"]').value;

        send_request_asycn (
          '../core/ajax/doctor-attendance-content.php', 
          'POST', 
          {
            employeeName : employeeName,
            logDate : logDate,
            departmentId : departmentId,
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }

    function loadReportForm() {
        let employeeName = document.querySelector('[name="employeeName"]');
        let csvEmployeeName = document.querySelector('[name="csvEmployeeName"]');
        csvEmployeeName.value = employeeName.value;

        let logDate = document.querySelector('[name="logDate"]');
        let csvLogDate = document.querySelector('[name="csvLogDate"]');
        csvLogDate.value = logDate.value;

        let departmentId = document.querySelector('[name="departmentId"]');
        let csvDepartmentId = document.querySelector('[name="csvDepartmentId"]');
        csvDepartmentId.value = departmentId.value;
    }

    // $(document).ready(function() {
        
    // });
</script>