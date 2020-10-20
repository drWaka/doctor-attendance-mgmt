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


// Additional Event Listeners

// Dynamic Form Controls
$(document).ready(function(){
  // Resource Variables
  const divisionElem = {
    "modal" : '#transaction-modal [name="divisionId"]',
    "report" : '.advance-filter [name="divisionId"]'
  };
  const departmentElem = {
    "modal" : '#transaction-modal [name="departmentId"]',
    "report" : '.advance-filter [name="departmentId"]'
  };
  const unitElem = {
    "modal" : '#transaction-modal [name="unitId"]',
    "report" : '.advance-filter [name="unitId"]'
  };
  const dynamicDepartmentLink = '../core/ajax/report-question-filter-division-department.php';
  const dynamicUnitLink = '../core/ajax/report-question-filter-department-unit.php';
  
  // Dynamic Report Filter
  $(document).on('change', divisionElem['report'], function() {
    // Update Department Filter
    let divisionId = $(this).val();
    let container = document.querySelector(departmentElem['report']);
    let data = {
      divisionId : divisionId,
      allowAllRec : 1
    }
    if (container !== null) {
      send_request_asycn(dynamicDepartmentLink, 'POST', data, departmentElem['report'], 'async-form');
    }
    
    setTimeout(() => {
      // Update Unit Filter
      let departmentId = (document.querySelector(departmentElem['report'])).value;
      let data = {
        departmentId : departmentId,
        allowAllRec : 1
      }
      send_request_asycn(dynamicUnitLink, 'POST', data, unitElem['report'], 'async-form');

      setTimeout(() => {
        pageConfig['page'] = 1;
        loadRecord();
      }, 500);
    }, 500);
  });

  $(document).on('change', departmentElem['report'], function() {
    // Update Unit Filter
    let departmentId = $(this).val();
    let data = {
      departmentId : departmentId,
      allowAllRec : 1
    }
    send_request_asycn(dynamicUnitLink, 'POST', data, unitElem['report'], 'async-form');
    setTimeout(() => {
      pageConfig['page'] = 1;
      loadRecord();
    }, 500);
  });

  // Dynamic Modal Field
  $(document).on('change', divisionElem['modal'], function() {
      // Update Department Field
      let divisionId = $(this).val();
      let data = {
        divisionId : divisionId,
        allowAllRec : 0
      }
      send_request_asycn (dynamicDepartmentLink, 'POST', data, departmentElem['modal'], 'async-form');
      
      setTimeout(() => {
        // Update Unit Field
        let departmentId = (document.querySelector(departmentElem['modal'])).value;
        let data = {
          departmentId : departmentId,
          allowAllRec : 0
        }
        send_request_asycn(dynamicUnitLink, 'POST', data, unitElem['modal'], 'async-form');
        
        setTimeout(() => {
          pageConfig['page'] = 1;
          loadRecord();
        }, 500);
      }, 500);
  });

  $(document).on('change', departmentElem['modal'], function() {
    // Update Unit Field
    let departmentId = $(this).val();
    let data = {
      departmentId : departmentId,
      allowAllRec : 0
    }
    send_request_asycn (dynamicUnitLink, 'POST', data, unitElem['modal'], 'async-form');
    setTimeout(() => {
      pageConfig['page'] = 1;
      loadRecord();
    }, 500);
  });
});