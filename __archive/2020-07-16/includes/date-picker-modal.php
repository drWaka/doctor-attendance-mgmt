<!-- HTML Date Input Replacement Modal -->
<div class="modal fade" id="datepicker-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <!-- Content Header -->
        <div class="content-header text-center">
          <div class="year">2020</div>
          <div class="date">WED, Jan 01</div>
        </div>

        <!-- Content Nav -->
        <div class="row content-nav">
          <div class="col-sm-2 col-3">
            <button class="btn btn-light btn-prev btn-nav"><i class="fa fa-chevron-left"></i></button>
          </div>
          <div class="col-sm-8 col-6">
            <div class="row">
              <div class="col-md-6 col-12">
                <label for="">CHOOSE MONTH:</label>
                <select name="month" class="form-control">
                  <option value="1">January</option>
                  <option value="2">February</option>
                  <option value="3">March</option>
                  <option value="4">April</option>
                  <option value="5">May</option>
                  <option value="6">June</option>
                  <option value="7">July</option>
                  <option value="8">August</option>
                  <option value="9">September</option>
                  <option value="10">October</option>
                  <option value="11">November</option>
                  <option value="12">December</option>
                </select>
              </div>

              <div class="col-md-6 col-12">
                <label for="">CHOOSE YEAR:</label>
                <select name="year" class="form-control">
                  <option value="2020">2020</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-sm-2 col-3 text-right">
            <button class="btn btn-light btn-next btn-nav"><i class="fa fa-chevron-right"></i></button>
          </div>
        </div>

        <div class="content-body">
          <div class="calendar-header">
            <div class="col-calendar">Sun</div>
            <div class="col-calendar">Mon</div>
            <div class="col-calendar">Tue</div>
            <div class="col-calendar">Wed</div>
            <div class="col-calendar">Thu</div>
            <div class="col-calendar">Fri</div>
            <div class="col-calendar">Sat</div>
          </div>
          <div class="calendar-body">

          </div>
        </div>

        <div class="content-footer row">
          <!-- <div class="col-4"><button class="btn btn-light date-node" data-content="" data-dismiss="modal"><i class="fas fa-calendar-times"></i> Clear</button></div> -->
          <div class="col-md-4 offset-md-2 col-6">
            <button class="btn btn-primary select-date" data-dismiss="modal"><i class="fas fa-calendar-day"></i> Select</button>
          </div>
          <div class="col-md-4 col-6">
            <button class="btn btn-danger" data-dismiss="modal" type="button"><i class="fas fa-times"></i> Cancel</button>
          </div>
        </div>

        <div class="row margin-top-xs">
          
        </div>
      </div>
    </div>
  </div>
</div>