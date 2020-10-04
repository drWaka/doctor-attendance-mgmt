<!-- Page Content Start -->
<div class="container-fluid app-content">
<div class="row">
    <div class="col-md-5">
    <h1 class="margin-top-sm page-header">
        User Responses By Date Range
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
<div class="advance-filter hide">
    <div class="row">
        <div class="col-4 margin-bottom-xs">
            <div class="row">
                <div class="col-12">
                    <label for="useDateRng" class="rangeLbl"> Start Response Date :</label>
                </div>
                <div class="col-12">
                    <input type="date" name="sessionStartDate" class="form-control filter" value="<?= date('Y-m-d'); ?>">
                </div>
            </div>
        </div>
        <div class="col-4 margin-bottom-xs">
            <div class="row">
                <div class="col-12">
                    <label for="useDateRng" class="rangeLbl"> End Response Date :</label>
                </div>
                <div class="col-12">
                    <input type="date" name="sessionEndDate" class="form-control filter" value="<?= date('Y-m-d'); ?>">
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
    </div>
    
    <div class="row">
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
    </div>
    
</div>


<div class="row margin-bottom">
    <div class="col-12">
    <table class="table table-hover table-dashed record-container">
        
    </table>
    </div>
    <div class="col-3">
        <form action="../core/requests/report-question-response.php" method="post" target="_blank">
            <input type="text" name="csvEmployeeName" hidden>
            <input type="text" name="csvQuestionMstrId" hidden>
            <input type="date" name="csvSessionStartDate" hidden>
            <input type="date" name="csvSessionEndDate" hidden>
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
        let sessionStartDate = document.querySelector('[name="sessionStartDate"]').value;
        let sessionEndDate = document.querySelector('[name="sessionEndDate"]').value;
        let questionMstrId = document.querySelector('[name="questionMstrId"]').value;
        let departmentId = document.querySelector('[name="departmentId"]').value;
        let divisionId = document.querySelector('[name="divisionId"]').value;
        console.log('suswit');
        send_request_asycn (
          '../core/ajax/report-question-response-range.php', 
          'POST', 
          {
            employeeName : employeeName,
            sessionStartDate : sessionStartDate,
            sessionEndDate : sessionEndDate,
            questionMstrId : questionMstrId,
            departmentId : departmentId,
            divisionId : divisionId,
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

        let sessionStartDate = document.querySelector('[name="sessionStartDate"]');
        let csvSessionStartDate = document.querySelector('[name="csvSessionStartDate"]');
        csvSessionStartDate.value = sessionStartDate.value;

        let sessionEndDate = document.querySelector('[name="sessionEndDate"]');
        let csvSessionEndDate = document.querySelector('[name="csvSessionEndDate"]');
        csvSessionEndDate.value = sessionEndDate.value;

        let questionMstrId = document.querySelector('[name="questionMstrId"]');
        let csvQuestionMstrId = document.querySelector('[name="csvQuestionMstrId"]');
        csvQuestionMstrId.value = questionMstrId.value;

        let divisionId = document.querySelector('[name="divisionId"]');
        let csvDivisionId = document.querySelector('[name="csvDivisionId"]');
        csvDivisionId.value = divisionId.value;

        let departmentId = document.querySelector('[name="departmentId"]');
        let csvDepartmentId = document.querySelector('[name="csvDepartmentId"]');
        csvDepartmentId.value = departmentId.value;
    }
</script>