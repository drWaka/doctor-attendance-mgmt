<!-- Page Content Start -->
<div class="container-fluid">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Question Responses
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-4">
            <input type="text" name="employeeName" class="form-control" placeholder="Employee Name" />
        </div>

        <div class="col-md-3">
        <div class="form-group">
            <button class="btn btn-info form-control filter-toggle">Toggle Filter</button>
        </div>
        </div>

    </div>
    </div>
</div>
<div class="row advance-filter hide">

    <div class="col-6">
    <div class="row">
        <div class="col-3">
            <label for="useDateRng" class="rangeLbl"> Response Date :</label>
        </div>
        <div class="col-9">
            <input type="date" name="sessionDate" class="form-control" value="<?= date('Y-m-d'); ?>">
        </div>
    </div>
    </div>
    
    <div class="col-6">
    <div class="row">
        <div class="col-3">
        <label for="useIdRng" class="rangeLbl"> Questionairre : </label>
        </div>
        <div class="col-9 row">
            <select name="questionMstrId" class="form-control" id="">
                <?php
                    $questions = QuestionMstr::index();

                    foreach ($questions as $question) {
                        echo "<option value='{$question['PK_questionMstr']}'>{$question['title']}</option>";
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
            <th>Employee ID</th>
            <th>Employe Name</th>
            <th>Survey Date</th>
            <th>Summary</th>
            <th>View</th>
        </tr>
        </thead>
        <tbody class="record-container">
        
        </tbody>
    </table>
    </div>
    <div class="col-4 offset-8">
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
        let employeeName = document.querySelector('[name="employeeName"]');
        let sessionDate = document.querySelector('[name="sessionDate"]');
        let questionMstrId = document.querySelector('[name="questionMstrId"]');

        send_request_asycn (
          '../core/ajax/report-question-response.php', 
          'POST', 
          {
            employeeName : employeeName,
            sessionDate : sessionDate,
            questionMstrId : questionMstrId
          }, 
          '.record-container', 
          'static-content'
        );
    }
</script>