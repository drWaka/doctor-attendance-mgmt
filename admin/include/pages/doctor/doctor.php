<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Doctor Profile Management
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-4">
            <input type="text" name="employeeName" class="form-control filter-search" placeholder="Doctor Name / ID" />
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
            <label> Department :</label>
        </div>
        <div class="col-12">
            <select name="departmentId" class="form-control" id="">
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

    <div class="col-4 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label> Include Deleted Records?</label>
        </div>
        <div class="col-12">
            <select name="isDeleted" class="form-control" id="">
                <option value="no" selected>No</option>
                <option value="yes">Yes</option>
            </select>
        </div>
    </div>
    </div>
</div>


<div class="row margin-bottom">
    <div class="col-12">
    <table class="table">
        <thead>
        <tr>
            <th>PRC ID</th>
            <th>Doctor Name</th>
            <th>Department</th>
            <th class='text-center'>Schedule</th>
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
                    title='Add New Doctor Record'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/doctor-select.php'
                    data-content='{
                        &quot;employeeId&quot; : &quot;new-rec&quot;
                    }'
                >Add Doctor</button>
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
        let departmentId = document.querySelector('[name="departmentId"]').value;
        let isDeleted = document.querySelector('[name="isDeleted"]').value;

        send_request_asycn (
          '../core/ajax/doctor-content.php', 
          'POST', 
          {
            employeeName : employeeName,
            departmentId : departmentId,
            isDeleted : isDeleted,
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }
</script>