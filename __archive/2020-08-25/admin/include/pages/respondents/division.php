<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Division Management
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-7">
            <input type="text" name="divisionName" class="form-control filter-search" placeholder="Division Name" />
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
            <th>Division Name</th>
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
                    title='Add New Division'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/division-select.php'
                    data-content='{
                        &quot;divisionId&quot; : &quot;new-rec&quot;
                    }'
                >Add Division</button>
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
        let divisionName = document.querySelector('[name="divisionName"]').value;
        send_request_asycn (
          '../core/ajax/division-content.php', 
          'POST', 
          {
            divisionName : divisionName, 
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }
</script>