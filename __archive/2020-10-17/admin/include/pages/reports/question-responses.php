<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        Question Responses
    </h1>
    </div>
    <div class="col-md-7 filter-fields">
    <div class="row">
        
        <div class="col-md-5 offset-md-4">
            <div class="input-group">
                <input type="text" name="employeeName" class="form-control" placeholder="Employee Name / ID" />
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
            <label for="useDateRng" class="rangeLbl"> Response Date :</label>
        </div>
        <div class="col-12">
            <input type="date" name="sessionDate" class="form-control filter" value="<?= date('Y-m-d'); ?>">
        </div>
    </div>
    </div>

    <div class="col-4 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label for="useDateRng" class="rangeLbl"> Cutoff Time :</label>
        </div>
        <div class="col-12">
            <input type="time" name="cutOffTime" class="form-control filter" value="23:59:59">
        </div>
    </div>
    </div>
    
    <div class="col-4 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
        <label for="useIdRng" class="rangeLbl"> Questionnaire : </label>
        </div>
        <div class="col-12">
            <select name="questionMstrId" class="form-control filter" id="">
                <?php
                    $questions = QuestionMstr::index();

                    foreach ($questions as $question) {
                        $selected = ($question['PK_questionMstr'] == '1') ? 'selected' : '';
                        echo "<option value='{$question['PK_questionMstr']}' {$selected}>{$question['title']}</option>";
                    }
                ?>
            </select>
        </div>
    </div>
    </div>


    <div class="col-4 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label for="useDateRng" class="rangeLbl"> Division :</label>
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
            <label for="useDateRng" class="rangeLbl"> Department :</label>
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
        <label for="useIdRng" class="rangeLbl"> Response Result : </label>
        </div>
        <div class="col-12">
            <select name="sessionRating" class="form-control filter" id="">
                <option value="all">All Result</option>
                <option value="without any symptom">Without Symptoms</option>
                <option value="with symptoms">With Symptoms</option>
                <option value="no response">No Response</option>
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
    <div class="col-3">
        <form action="../core/requests/report-question-response.php" method="post" target="_blank">
            <input type="text" name="csvEmployeeName" hidden>
            <input type="text" name="csvQuestionMstrId" hidden>
            <input type="date" name="csvSessionDate" hidden>
            <input type="date" name="csvcutOffTime" hidden>
            <input type="text" name="csvSessionRating" hidden>
            <input type="text" name="csvDivisionId" hidden>
            <input type="text" name="csvDepartmentId" hidden>
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
        let sessionDate = document.querySelector('[name="sessionDate"]').value;
        let cutOffTime = document.querySelector('[name="cutOffTime"]').value;
        let questionMstrId = document.querySelector('[name="questionMstrId"]').value;
        let sessionRating = document.querySelector('[name="sessionRating"]').value;
        let departmentId = document.querySelector('[name="departmentId"]').value;
        let divisionId = document.querySelector('[name="divisionId"]').value;

        send_request_asycn (
          '../core/ajax/report-question-response.php', 
          'POST', 
          {
            employeeName : employeeName,
            sessionDate : sessionDate,
            cutOffTime : cutOffTime,
            questionMstrId : questionMstrId,
            departmentId : departmentId,
            divisionId : divisionId,
            sessionRating : sessionRating,
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

        let sessionDate = document.querySelector('[name="sessionDate"]');
        let csvSessionDate = document.querySelector('[name="csvSessionDate"]');
        csvSessionDate.value = sessionDate.value;

        let cutOffTime = document.querySelector('[name="cutOffTime"]');
        let csvcutOffTime = document.querySelector('[name="csvcutOffTime"]');
        csvcutOffTime.value = cutOffTime.value;

        let questionMstrId = document.querySelector('[name="questionMstrId"]');
        let csvQuestionMstrId = document.querySelector('[name="csvQuestionMstrId"]');
        csvQuestionMstrId.value = questionMstrId.value;

        let divisionId = document.querySelector('[name="divisionId"]');
        let csvDivisionId = document.querySelector('[name="csvDivisionId"]');
        csvDivisionId.value = divisionId.value;

        let departmentId = document.querySelector('[name="departmentId"]');
        let csvDepartmentId = document.querySelector('[name="csvDepartmentId"]');
        csvDepartmentId.value = departmentId.value;

        let sessionRating = document.querySelector('[name="sessionRating"]');
        let csvSessionRating = document.querySelector('[name="csvSessionRating"]');
        csvSessionRating.value = sessionRating.value;
    }

    // $(document).ready(function() {
        
    // });
</script>