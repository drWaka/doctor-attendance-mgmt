// Function for the number refresh at the queue board
function send_request_asycn (url, method, data, container, transName, content_type = 'application/x-www-form-urlencoded', isCached = true) {
  var ajax_paramemter = {
    url : url,
    method : method,
    data : data,
    success : function(result) {
      setTimeout(function() { manageLoading('hide'); }, 200);
      console.log(result);
      result = JSON.parse(result);
      console.log(result);


      if (result['success'] == 'success') {

        if (transName == 'async-form') {
          if (result['contentType'] == 'dynamic-content') {
            $(container).html(result['content'].form);
          } else if (result['contentType'] == 'modal') {
            initializeModal(result['content'].modal);
          }
        } else if (transName == 'static-content') {
          $(container).html(result['content']);
        } else if (transName == 'record-content') {
          if (typeof pageConfig != undefined) {
            pageConfig['total'] = result['content']['total'];
            manageNav();
          }
          $(container).html(result['content']['record']);
        }

        // Determine if the container contains form
        let containerNode = document.querySelector(container);
        let formNode = containerNode.querySelector('form');

        if (formNode !== null) {
          // validate the Form node within the container
          initialize_form_validation(container);
        }
      } else {
        initializeModal(result['content'].modal);
      }      
      
    },
    beforeSend : function () {
      manageLoading('show');
    },
    error : function () {
      // ToDo : Update. Show A Modal Error Instead
      console.log('Asynchronous Request Failed');
    }
  }

  // console.log(ajax_paramemter);
  if (content_type == 'file-image') {
    ajax_paramemter.cache       = false;
    ajax_paramemter.processData = false;  
    ajax_paramemter.contentType = false;
  } else {
    ajax_paramemter.contentType = content_type;
  }

  // Send AJAX Request
  $.ajax(ajax_paramemter);
}

// Error Handling Function
function initializeModal(element) {
  console.log('waka');
  var modalContainer = document.querySelector('.modal-container');
  
  if (modalContainer == null) {
    let body = document.querySelector('body');
    let elementContainer = document.createElement('div');
    elementContainer.classList.add('modal-container');
    body.appendChild(elementContainer);
  }

  $('.modal-container').html(element);
  $('#transaction-modal').attr("data-backdrop", "static");
  $('#transaction-modal').attr("data-keyboard", "false");
  $('#transaction-modal').modal('show');
}

function manageLoading(flag) {
  let loadingCover = document.querySelector('.loading-cover');
  if (loadingCover != null) {
    if (flag == 'show') {
      if (loadingCover.className.indexOf('show') == -1) {
        loadingCover.classList.add('show');
      }
    } else {
      loadingCover.classList.remove('show');
    }
  }
}