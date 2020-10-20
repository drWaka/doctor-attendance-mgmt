<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Respondent Management
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-4">
            <input type="text" name="employeeName" class="form-control filter-search" placeholder="Employee Name / ID" />
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
            <label> Division :</label>
        </div>
        <div class="col-12">
            <select name="divisionId" class="form-control" id="">
                <option value="all">All Division</option>
                <?php
                    $divisionQry = "SELECT * FROM mscdivision";
                    $divisionRes = $connection -> query($divisionQry);

                    if ($divisionRes -> num_rows > 0) {
                        while ($divisionRow = $divisionRes -> fetch_assoc()) {
                            echo "<option value='{$divisionRow['PK_mscdivision']}'>{$divisionRow['description']}</option>";
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
            <label> Department :</label>
        </div>
        <div class="col-12">
            <select name="departmentId" class="form-control filter" id="">
                <option value="all">All Department</option>
            </select>
        </div>
    </div>
    </div>

    <div class="col-4 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label> Unit :</label>
        </div>
        <div class="col-12">
            <select name="unitId" class="form-control filter" id="">
                <option value="all">All Unit</option>
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
            <th>Employee ID</th>
            <th>Employe Name</th>
            <th class='text-center'>Unit</th>
            <th class='text-center'>Department</th>
            <th class='text-center'>Division</th>
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
                    title='Add New Respondent Record'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/respondent-select.php'
                    data-content='{
                        &quot;employeeId&quot; : &quot;new-rec&quot;
                    }'
                >Add Respondent</button>
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
        let divisionId = document.querySelector('[name="divisionId"]').value;
        let unitId = document.querySelector('[name="unitId"]').value;

        send_request_asycn (
          '../core/ajax/respondent-content.php', 
          'POST', 
          {
            employeeName : employeeName,
            departmentId : departmentId,
            divisionId : divisionId,
            unitId : unitId,
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }
</script>