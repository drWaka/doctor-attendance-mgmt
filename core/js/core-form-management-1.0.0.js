function initialize_form_validation (formContainer) {
  // Modal Node
  let formContainerNode = document.querySelector(formContainer);

  // The Form inside the modal
  let form = formContainerNode.querySelector('form');
  let formName = form.getAttribute('form-name');

  // The Submit Button inside the modal
  let submitBtn = formContainerNode.querySelector('.form-submit-button');
  submitBtn.addEventListener('click', function () {
    validate_form(form);
  });

  // The Form-Controls inside the form
  let dataField = form.querySelectorAll('.form-control');
  console.log(dataField);

  // Condition that will determine on what are the field that must be used everytime the function is invoked
  if (formName == 'login-form') {
    // ToDo : Need to Find a way on how to validate Non-Form Elements
    // add_input_event(dataField[0], 'Employee ID', 'text-int', true, form);
    // add_input_event(dataField[1], 'Birthdate', 'date', true, form);
  } else if (formName == 'survey-form') {
    var description = 'Response';
    if (dataField[0].getAttribute('field-desc') != null) {
      description = dataField[0].getAttribute('field-desc');
    }
    var dataType = 'text-int';
    if (dataField[0].getAttribute('field-type') != null) {
      dataType = dataField[0].getAttribute('field-type');
    }
    var isRequired = 'text-int';
    if (dataField[0].getAttribute('field-required') != null) {
      isRequired = dataField[0].getAttribute('field-required');
      isRequired = (isRequired == '1')
        ? true
        : false;
    }
    add_input_event(dataField[0], description, dataType, isRequired, form);
  } else if (formName == 'admin-login') {
    add_input_event(dataField[0], 'User ID', 'text-int', true, form);
    add_input_event(dataField[1], 'Password', 'text-int', true, form);
  }
}

function validate_form(form) {
  // let update_form = form;
  let inputField = form.querySelectorAll('.form-control');
  let formName = form.getAttribute('form-name');
  var formValidity = true;

  if (formName == 'login-form') {
    // ToDo : Need to Find a way on how to validate Non-Form Elements
    // formValidity = final_data_check(inputField[0], 'Employee ID', 'text-int', true, formValidity);
    // formValidity = final_data_check(inputField[1], 'Birthdate', 'date', true, formValidity);
  } else if (formName == 'survey-form') {
    var description = 'Response';
    if (inputField[0].getAttribute('field-desc') != null) {
      description = inputField[0].getAttribute('field-desc')
    }
    var dataType = 'text-int';
    if (inputField[0].getAttribute('field-type') != null) {
      dataType = inputField[0].getAttribute('field-type')
    }
    var isRequired = false;
    if (inputField[0].getAttribute('field-required') != null) {
      isRequired = inputField[0].getAttribute('field-required')
    }
    formValidity = final_data_check(inputField[0], description, dataType, isRequired, formValidity);
  } else if (formName == 'admin-login') {
    formValidity = final_data_check(inputField[0], 'User ID', 'text-int', true, formValidity);
    formValidity = final_data_check(inputField[1], 'Password', 'text-int', true, formValidity);
  } 

  if (formValidity == true) {
    let submitType = form.getAttribute('submit-type');
    
    console.log(submitType);
    if (submitType == 'synchronous') {
      form.submit();
    } else {
      let link = form.getAttribute('action');
      let dataFieldValue = new FormData(form);
      let contentType = 'file-image';
      let transName = form.getAttribute('tran-type') == null
        ? 'modal-rec'
        : form.getAttribute('tran-type');
      
      // ToDo : Improve getting automatically the parent element 
      // when an non-modal async form is submitted
      let elementContainer = (form.getAttribute('tran-container') == null)
        ? '.modal-container'
        : '.' + form.getAttribute('tran-container');

      console.log(dataFieldValue);
      send_request_asycn(link, 'POST', dataFieldValue, elementContainer, transName, contentType);
    }
  }
}

// Function that will give event listeners to input fields when called
function add_input_event(element, name, data_type, required, form) {
  // the event listenter that validates a particular elem
  element.addEventListener('blur', function() {
    // function that will validate the field according to its type
    var field_elem = check_data(element, name, data_type, required);
    if (field_elem.valid == 0) {
      // if the value of the field is invalid an error will be appended in the field's parent element
      add_error(field_elem);
    }
  });

  // element.addEventListener('keyup', function(e) {
  //   e.preventDefault();
  //   if (e.keyCode == 13) {
  //     validate_form(form);
  //   }
  // });
}

function final_data_check(element, name, data_type, required, form_validity) {
  var elem = check_data(element, name, data_type, required);
  if (elem.valid == 0) {
    add_error(elem);
    return false;
  } else {
    return form_validity;
  }
}

function add_error(obj) {
  var parent = obj.element.parentNode;
  var element = obj.element;
  if (element.className.indexOf('is-invalid') == -1) {
    element.classList.add('is-invalid');

    var error_icon = document.createElement('span');
    error_icon.classList.add('fa');
    error_icon.classList.add('fa-times');
    error_icon.classList.add('invalid-icon');
    parent.appendChild(error_icon);
    var error_msg = document.createElement('div');
    error_msg.textContent = obj.err_msg;
    error_msg.classList.add('invalid-feedback');
    parent.appendChild(error_msg);
  }
}

function remove_error(elem) {
  var parent = elem.parentNode;
  if(elem.className.indexOf('is-invalid') > -1) {
    elem.classList.remove('is-invalid');
    var child = parent.childNodes;
    
    /*
     *
     * Commented Temporarily, Might be implemented in the future
     *
     */
    
    // while (child[3]) {
    //   parent.removeChild(child[3]);
    // }

    for (var index = 0 ; index < child.length ; index++) {
      // console.log(child[index]);
      let i = index;
      if (child[i].nodeType == 1) {
        if (
          child[i].className.indexOf('invalid-icon') > -1 || 
          child[i].className.indexOf('invalid-feedback') > -1
        ) {
          // console.log(child[i]);
          parent.removeChild(child[i]);
        }
      }
    }

    for (var index = 0 ; index < child.length ; index++) {
      // console.log(child[index]);
      let i = index;
      if (child[i].nodeType == 1) {
        if (
          child[i].className.indexOf('invalid-icon') > -1 || 
          child[i].className.indexOf('invalid-feedback') > -1
        ) {
          // console.log(child[i]);
          parent.removeChild(child[i]);
        }
      }
    }
  }
}

function check_data(elem, label, type, required) {
  // removes the previous error to prevent error redundancy
  remove_error(elem);
  var response = {
    valid : 1,
    err_msg : '',
    element : elem
  };

  function required_func() {

    if (elem.value == '') {
      response.valid = 0;
      response.err_msg = 'Please enter a ' + label;
    } else {
      response.valid = 1;
    }
  }
  if (type == 'text') {
    var value = elem.value.split("");
    for (var index = 0 ; index < value.length ; index++) {
      if (!isNaN(value[index]) && value[index] != ' ') {
        response.valid = 0;
        response.err_msg = label + ' must not contain numbers';
        break;
      }
    }
    if (response.valid != 0) {
      if (required == true) {
        required_func();
      } else {
        response.valid = 1;
      }
    }
  } else if (type == 'int') {
    if (isNaN(elem.value)) {
      response.valid = 0;
      response.err_msg = 'Please enter a valid ' + label;
    } else {
      if (response.valid !== 0) {
        if (required == true) {
          required_func();
        } else {
          response.valid = 1;
        }
      }
    }
  } else if (type == 'double') {
    var values = elem.value.split(".");
    for (var index = 0 ; index < values.length ; index++) {
      (function() {
        if (isNaN(values[index])) {
          response.valid = 0;
          response.err_msg = 'Please enter a valid ' + label;
        }
      })();
    }

    if (response.valid !== 0) {
      if (required == true) {
        required_func();
      } else {
        response.valid = 1;
      }
    }
  } else if (type == 'text-int' || type == 'password') {
    if (required == true) {
      required_func();
    } else {
      response.valid = 1;
    }
  } else if (type == 'date') {
    // The value of the input
    var value = elem.value.split("-");

    // assigned the user input into a variable for comparison purposes
    var date_input = new Date();
    date_input.setFullYear(value[0], value[1] - 1, value[2]);

    // assigned the date last 18 years ago which will be our minimum year allowed
    var date_min = new Date();
    date_min.setFullYear(date_min.getFullYear() - 18, date_min.getMonth(), date_min.getDate());
    // console.log(date_min);
    // console.log(value);
    if (date_input > date_min) {
      if (label == 'Date of Birth') {
        response.valid = 0;
        response.err_msg = label + ' must be atleast earlier than 18 years';
      }
    } else if (required == true) {
      required_func();
    }
  } else if (type == 'email') {
    if (required == true) {
      required_func();
    }
  } else {
    type = type.split('-');

    if (type[0] == 'file') {
      if (required == true) {
        required_func();
      } else {
        response.valid = 1;
      }

      var fileLimit = {
        size : 0,
        label : ''
      };

      if (type[1] == 'doc') {
        fileLimit.size = 15000000;
        fileLimit.label = '15MB';
      } else if (type[1] == 'img') {
        fileLimit.size = 15000000;
        fileLimit.label = '15MB';
      }

      if (response.valid == 1) {
        if (response.element.files[0]) {
          if (response.element.files[0].size > fileLimit.size) {
            response.valid = 0;
            response.err_msg = 'File upload size must not exceed to ' + fileLimit.label;
          } else {
            var extensionName = elem.value.split('.');
            extensionName = extensionName[extensionName.length - 1];

            if (type[1] == 'doc') {
              if (extensionName != 'doc' && extensionName != 'docx' && extensionName != 'pdf') {
                response.valid = 0;
                response.err_msg = 'Please choose a valid ' + label;
              }
            } else if (type[1] == 'img') {
              if (extensionName != 'jpg' && extensionName != 'jpeg' && extensionName != 'gif' && extensionName != 'png') {
                response.valid = 0;
                response.err_msg = 'Please choose a valid ' + label;
              }
            }
          }
        }
      }
    }
  }

  // Create a code that will validate a date
  return response;
}

// Event Listener for Synchronous Forms
$('[submit-type="synchronous"]').on('keyup', '.form-control', function(e) {
  if (e.keyCode == 13) {
    var formNode = ($(this))[0];
    while (formNode.tagName.toLowerCase() !== 'form') {
      formNode = formNode.parentNode;
    }

    validate_form(formNode);
  }
});