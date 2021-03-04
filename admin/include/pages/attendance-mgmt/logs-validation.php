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

    <div class="col-3 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label for="useDateRng" class="rangeLbl"> Document Date :</label>
        </div>
        <div class="col-12">
            <input type="date" name="docDate" class="form-control filter" value="<?= date('Y-m-d'); ?>">
        </div>
    </div>
    </div>

    <div class="col-3 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label for="useDateRng" class="rangeLbl"> Document No.:</label>
        </div>
        <div class="col-12">
            <input type="input" name="docNo" class="form-control filter">
        </div>
    </div>
    </div>

    <div class="col-6 margin-bottom-xs">
    <div class="row">
        <div class="col-12">
            <label for="useDateRng" class="rangeLbl"> Document Status :</label>
        </div>
        <div class="col-12">
            <div class="row cbox-container">
                <div class="col-3">
                    <label class="cbox-item"><input type="checkbox" class="form-control filter" name='docStat[]' value="saved" checked disabled> Saved</label>
                </div>
                <div class="col-3">
                    <label class="cbox-item"><input type="checkbox" class="form-control filter" name='docStat[]' value="posted" checked> Posted</label>
                </div>
                <div class="col-3">
                    <label class="cbox-item"><input type="checkbox" class="form-control filter" name='docStat[]' value="cancelled"> Cancelled</label>
                </div>
                <div class="col-3">
                    <label class="cbox-item"><input type="checkbox" class="form-control filter" name='docStat[]' value="voided"> Voided</label>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>


<div class="row margin-bottom">
    <div class="col-12">
    <table class="table table-hover table-dashed">
        <thead>
        <tr>
            <th>Document No.</th>
            <th>Document Date</th>
            <th>Doctor</th>
            <th class="text-center">Status</th>
            <th class="text-center">Manage Logs</th>
            <th class="text-center">Manage Document</th>
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
                    title='Add New Document Record'
                    trans-name='async-form'
                    data-target='.modal-container'
                    data-link='../core/ajax/logs-validation-select.php'
                    data-content='{
                        &quot;recordId&quot; : &quot;new-rec&quot;
                    }'
                >Add New Document</button>
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
        let docDate = document.querySelector('[name="docDate"]').value;
        let docNo = document.querySelector('[name="docNo"]').value;
        let docStatus = document.querySelectorAll('[name="docStat[]"]');
        
        let docStatusVal = [];
        docStatus.forEach(element => {
            docStatusVal[docStatusVal.length] = element.checked == true ? 1 : 0;
        });        

        send_request_asycn (
          '../core/ajax/logs-validation-content.php', 
          'POST', 
          {
            employeeName : employeeName,
            docDate : docDate,
            docNo : docNo,
            isPosted : docStatusVal[1],
            isCancelled : docStatusVal[2],
            isVoided : docStatusVal[3],
            pageLimit : pageConfig.limit,
            currentPage : pageConfig.page
          }, 
          '.record-container', 
          'record-content'
        );
    }

    window.onload = function() {
        $(document).on('focus', "[name='employeeName']", function() {
            let searchResult = (($(this)[0]).parentNode).querySelector('.search-result');
            searchResult.classList.add('in');
        });

        $(document).on('focusout', "[name='employeeName']", function() {
            setTimeout(() => {
                let searchResult = (($(this)[0]).parentNode).querySelector('.search-result');
                searchResult.classList.remove('in');
            }, 200);
        });

        $(document).on('click', '.search-result-item', function() {
            let itemNode = $(this)[0];
            let formGroupNode = itemNode.parentNode;
            while (!(formGroupNode.className.indexOf('form-group') > -1)) {
                formGroupNode = formGroupNode.parentNode;
            }
            let employeeIdNode = formGroupNode.querySelector("[name='employeeId']");
            let employeeNameNode = formGroupNode.querySelector("[name='employeeName']");
            employeeIdNode.value = itemNode.getAttribute('employee-id');
            employeeNameNode.value = itemNode.getAttribute('employee-name');
        });

        $(document).on('keyup', 'input[name="employeeName"]', function() {
            let inputNode = $(this)[0];
            let inputNodeContainer = inputNode.parentNode;

            let method = 'POST';
            let url = '../core/ajax/doctor-search.php';
            let data = {
                employeeName : inputNode.value
            }
            let callback = function(httpResult) {
                console.log(httpResult);
                
                let logsMgmtForm = document.querySelector('.logs-validation-form');
                if (logsMgmtForm !== undefined) {
                    let searchResult = logsMgmtForm.querySelector('.search-result');
                    searchResult.textContent = '';
                    if (httpResult.content.total > 0) {
                        httpResult.content.record.forEach(function(value){
                            let nodeValue = value['employeeName'] + " &minus; " + value['employeeNo'];

                            // Search Result Item Element
                            let resultItemNode = document.createElement('div');
                            resultItemNode.classList.add('search-result-item');
                            resultItemNode.classList.add('row');
                            resultItemNode.setAttribute('employee-id', value['employeeId']);
                            resultItemNode.setAttribute('employee-name', value['employeeName']);
                            resultItemNode.innerHTML = nodeValue;

                            searchResult.appendChild(resultItemNode);
                        });
                    } else {
                        // Search Result Item Element
                        let resultItemNode = document.createElement('div');
                        resultItemNode.classList.add('search-result-item');
                        resultItemNode.classList.add('row');
                        resultItemNode.classList.add('text-center');
                        resultItemNode.textContent = 'No Matches Found';

                        searchResult.appendChild(resultItemNode);
                    }
                }
            }
            sendXHR(url, method, data, callback);
        });
    };
</script>