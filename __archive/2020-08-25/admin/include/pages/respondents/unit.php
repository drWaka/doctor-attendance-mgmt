<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Unit Management
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        <div class="col-md-5 offset-md-4">
            <input type="text" name="unitName" class="form-control filter-search" placeholder="Unit Name" />
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
            <select name="departmentId" class="form-control filter" id="">
                <option value="all">All Department</option>
                <?php
                    $department = MscDepartment::index();
                    if (is_array($department)) {
                        if (count($department) > 0) {
                            foreach ($department as $row) {
                                echo "<option value='{$row['PK_mscdepartment']}'>{$row['description']}</option>";
                            }
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
            <th>System ID</th>
            <th>Unit Name</th>
            <th>Department</th>
            <th class='text-center'>Recipients</th>
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
                    title='Add New Department'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/unit-select.php'
                    data-content='{
                        &quot;unitId&quot; : &quot;new-rec&quot;
                    }'
                >Add Unit</button>
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
        console.log('Waka');
        let unitName = document.querySelector('[name="unitName"]').value;
        let departmentId = document.querySelector('[name="departmentId"]').value;
        console.log(departmentId);
        send_request_asycn (
          '../core/ajax/unit-content.php', 
          'POST', 
          {
            unitName : unitName, 
            departmentId : departmentId, 
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }
</script>