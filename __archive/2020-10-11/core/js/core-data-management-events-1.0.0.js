$(document).ready(function() {
  // Update Currently Fetched Record Event Listener
  $(document).on('click', '.transaction-btn', function() {
    let container = $(this).attr('data-target');
    let link = $(this).attr('data-link');
    let transName = $(this).attr('trans-name');
    let content = JSON.parse($(this).attr('data-content'));

    // console.log(container);
    // console.log(link);
    // console.log(transName);
    // console.log(content);
    
    send_request_asycn (link, 'POST', content, container, transName);
  });

  $(document).on('click', '.btn-trigger', function() {
    loadRecord();
  });
});

// Exceptions/Custom JS Scripts
$(document).ready(function(){
  // Custom Event Listenter for Division Element for the Respondent Masterlist
  $(document).on('change', '.advance-filter [name="divisionId"]', function() {
    // Update Department Field
    let divisionId = $(this).val();
    // Test if the Container Exists
    let container = '.advance-filter [name="departmentId"]';
    let containerElem = document.querySelector(container);
    if (containerElem !== null) {
      send_request_asycn (
        '../core/ajax/report-question-filter-division-department.php', 
        'POST', 
        {
            divisionId : divisionId
        }, 
        container, 
        'async-form'
      );
    }
    
    setTimeout(() => {
      // Test if the Container Exists
      let container = '.advance-filter [name="unitId"]';
      let containerElem = document.querySelector(container);
      if (containerElem !== null) {
         // Update Unit Field
        let departmentId = (document.querySelector('.advance-filter [name="departmentId"]')).value;
        send_request_asycn (
            '../core/ajax/report-question-filter-department-unit.php', 
            'POST', 
            {
                departmentId : departmentId
            }, 
            container, 
            'async-form'
        );
        setTimeout(() => {
          pageConfig['page'] = 1;
          loadRecord();
        }, 200);
      }
    }, 200);
  });

  // For the Respondent Details
  $(document).on('change', '#transaction-modal [name="divisionId"]', function() {
      // Update Department Field
      let divisionId = $(this).val();
      send_request_asycn (
          '../core/ajax/report-question-filter-division-department.php', 
          'POST', 
          {
              divisionId : divisionId
          }, 
          '#transaction-modal [name="departmentId"]', 
          'async-form'
      );
      setTimeout(() => {        
        // Test if the Container Exists
        let container = '#transaction-modal [name="unitId"]';
        let containerElem = document.querySelector(container);
        if (containerElem !== null) {
          // Update Unit Field
          let departmentId = (document.querySelector('#transaction-modal [name="departmentId"]')).value;
          send_request_asycn (
              '../core/ajax/report-question-filter-department-unit.php', 
              'POST', 
              {
                  departmentId : departmentId
              }, 
              container, 
              'async-form'
          );
          setTimeout(() => {
            pageConfig['page'] = 1;
            loadRecord();
          }, 200);
        }
      }, 200);
  });

  // Custom Event Listenter for Department Element for the Respondent Masterlist
  $(document).on('change', '.advance-filter [name="departmentId"]', function() {
      let departmentId = $(this).val();
      send_request_asycn (
          '../core/ajax/report-question-filter-department-unit.php', 
          'POST', 
          {
              departmentId : departmentId
          }, 
          '.advance-filter [name="unitId"]', 
          'async-form'
      );
      setTimeout(() => {
        pageConfig['page'] = 1;
        loadRecord();
      }, 500);
  });

  // For the Respondent Details
  $(document).on('change', '#transaction-modal [name="departmentId"]', function() {
      let departmentId = $(this).val();
      send_request_asycn (
          '../core/ajax/report-question-filter-department-unit.php', 
          'POST', 
          {
              departmentId : departmentId
          }, 
          '#transaction-modal [name="unitId"]', 
          'async-form'
      );
      setTimeout(() => {
        pageConfig['page'] = 1;
        loadRecord();
      }, 500);
  });
});