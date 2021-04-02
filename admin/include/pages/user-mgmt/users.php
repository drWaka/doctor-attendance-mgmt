<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        User Management
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-4">
            <input type="text" name="userName" class="form-control filter-search" placeholder="User Name / ID" />
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

    <div class="col-6 margin-bottom-xs">
    <div class="row">
        <div class="col-4">
            <label> User Type :</label>
        </div>
        <div class="col-8">
            <select name="userTypeId" class="form-control filter" id="">
                <option value="all">All User Type</option>
                <?php
                    $userTypes = UserType::index();
                    if (is_array($userTypes)) {
                        if (count($userTypes) > 0) {
                            foreach($userTypes as $userType) {
                                echo "<option value='{$userType['PK_userType']}' class='capitalize'>{$userType['description']}</option>";
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
    <table class="table">
        <thead>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>User Type</th>
            <th>Account Status</th>
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
            <div class="col-sm-6">
            <button class='btn btn-info transaction-btn w-100'
                trans-name='async-form'
                data-link='../core/ajax/user-mgmt-user-select.php'
                data-content='{
                    &quot;userMstrId&quot; : &quot;new&quot;

                }'
                data-target='.modal-container'
                title='Edit User'
            ><i class='fa fa-plus'></i> Add New User</button>
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
        let userName = document.querySelector('[name="userName"]').value;
        let userTypeId = document.querySelector('[name="userTypeId"]').value;

        send_request_asycn (
          '../core/ajax/user-mgmt-user-content.php', 
          'POST', 
          {
            userName : userName,
            userTypeId : userTypeId,
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }
</script>