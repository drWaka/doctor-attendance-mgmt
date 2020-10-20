function updateCaledar(activeDate) {
  // Convert the ActiveDate to JS Date Obj
  var activeDate = new Date(activeDate);
  
  // Initialize Arrow Navigation Values 
  setArrowNavValues(activeDate);

  // Initialize Dropdown Navigation Values
  setDropdownNavValues(activeDate);

  // Month & Day Array
  let monthNames = [
    "Jan", "Feb", "Mar", "Apr",
    "May", "Jun", "Jul", "Aug",
    "Sep", "Oct", "Nov", "Dec"
  ];
  let dayNames = [
    "Sunday", "Monday", "Tuesday",
    "Wednesday", "Thursday", "Friday",
    "Saturday"
  ];

  // Calendar Modal Nodes
  let calendarNodes = {
    // Modal Header
    header : {
      year : document.querySelector('#datepicker-modal .modal-body .content-header .year'),
      date : document.querySelector('#datepicker-modal .modal-body .content-header .date')
    },

    // Modal Body
    body : document.querySelector('#datepicker-modal .modal-body .content-body .calendar-body')
  };


  // Set Modal Header Content
  calendarNodes["header"]["year"].textContent = activeDate.getFullYear();
  calendarNodes["header"]["date"].textContent = dayNames[activeDate.getDay()] + ', ' + monthNames[activeDate.getMonth()] + ' ' + activeDate.getDate();
  
  // Empty the Calendar Body
  calendarNodes["body"].innerHTML = '';

  // Set the dateIndex to the firstday of the month
  var dateIndex = new Date(activeDate.getFullYear() + '-'+ (activeDate.getMonth() + 1) + '-01');
  dateIndex.setDate(dateIndex.getDate() - dateIndex.getDay());
  let dateLimit = new Date(dateIndex);
  dateLimit.setDate(dateLimit.getDate() + 42);

  while (dateIndex < dateLimit) {
    // Create .col-calendar
    var dateElem = document.createElement('div');
    dateElem.classList.add('col-calendar');
    dateElem.classList.add('date-node');

    // Determine if the dateIndex is the activeDate
    if (
      dateIndex.getDate() == activeDate.getDate() &&
      dateIndex.getMonth() == activeDate.getMonth() &&
      dateIndex.getFullYear() == activeDate.getFullYear()
    ) {
      dateElem.classList.add('active');
    }

    // To set the valid format for HTML Date Inputs
    let yearVal = dateIndex.getFullYear();
    let monthVal = (dateIndex.getMonth() < 9)
      ? '0' + (dateIndex.getMonth() + 1)
      : (dateIndex.getMonth() + 1);
    let dayVal = (dateIndex.getDate() < 10)
      ? '0' + dateIndex.getDate()
      : dateIndex.getDate();

    dateElem.setAttribute('data-content', (yearVal + '-' + monthVal + '-' + dayVal));

    // Set Text Content
    let textContent = document.createTextNode(dateIndex.getDate());

    // Append Text Content
    dateElem.appendChild(textContent);

    // Append .col-calendar
    calendarNodes["body"].appendChild(dateElem);

    // Increment Date by a day
    dateIndex.setDate(dateIndex.getDate() + 1);
  }
}

// Set Nav Next Values
function setArrowNavValues(activeDate) {
  var activeDate = new Date(activeDate);
  // Calendar Navigation Nodes
  let calendarNav = {
    prev : document.querySelector('#datepicker-modal .content-nav .btn-prev'),
    next : document.querySelector('#datepicker-modal .content-nav .btn-next')
  }

  // To set the valid format for HTML Date Inputs
  var navDateVal = {
    prev : {
      year : '',
      month : '',
      day : ''
    },
    next : {
      year : '',
      month : '',
      day : ''
    }
  } 

  // Previous Button
  if ((activeDate.getMonth() + 1) == 1) {
    // Detect if the Current Month is January
    // Set the Prev Date to Prev Year
    navDateVal['prev']['year'] = (activeDate.getFullYear() - 1);
    navDateVal['prev']['month'] = '12';
    navDateVal['prev']['day'] = activeDate.getDate();
  } else if ((activeDate.getMonth() + 1) == 3) {
    // Detect if the current Month is March
    navDateVal['prev']['year'] = activeDate.getFullYear();
    navDateVal['prev']['month'] = activeDate.getMonth();
    navDateVal['prev']['day'] = activeDate.getDate();
    if (activeDate.getDate() > 28) {
      // Set to the Max Day of Feb (28)
      navDateVal['prev']['day'] = '28';
      if (activeDate.getFullYear() % 4 == 0) {
        // Check if the Year is leap year
        navDateVal['prev']['day'] = '29';
        if (activeDate.getFullYear() % 100 == 0) {
          if (!activeDate.getFullYear() % 400 == 0) {
            // Special Rules if Leap Year
            navDateVal['prev']['day'] = '28';
          }
        }
      }
    }
  } else if (
    (activeDate.getMonth() + 1) == 5 ||
    (activeDate.getMonth() + 1) == 7 ||
    (activeDate.getMonth() + 1) == 10 ||
    (activeDate.getMonth() + 1) == 12
  ) {
    navDateVal['prev']['year'] = activeDate.getFullYear();
    navDateVal['prev']['month'] = activeDate.getMonth();
    navDateVal['prev']['day'] = activeDate.getDate();
    if (activeDate.getDate() > 30) {
      // Set to the Maximum Value of 30 Days Months
      navDateVal['prev']['day'] = '30';
    }
  } else {
    // Default Choice: Just Carry Over the Date and Select Prev Month
    navDateVal['prev']['year'] = activeDate.getFullYear();
    navDateVal['prev']['month'] = activeDate.getMonth();
    navDateVal['prev']['day'] = activeDate.getDate();
  }

  calendarNav["prev"].setAttribute("data-content",
    navDateVal['prev']['year'] + '-' + navDateVal['prev']['month'] + '-' + navDateVal['prev']['day']
  );

  // Next Button
  if ((activeDate.getMonth() + 1) == 12) {
    // Detect if the Current Month is December
    // Set the Next Date to Next Year
    navDateVal['next']['year'] = (activeDate.getFullYear() + 1);
    navDateVal['next']['month'] = '1';
    navDateVal['next']['day'] = activeDate.getDate();
  } else if ((activeDate.getMonth() + 1) == 1) {
    // Detect if the current Month is January
    navDateVal['next']['year'] = activeDate.getFullYear();
    navDateVal['next']['month'] = (activeDate.getMonth() + 2);
    navDateVal['next']['day'] = activeDate.getDate();
    if (activeDate.getDate() > 28) {
      // Set to the Max Day of Feb (28)
      navDateVal['next']['day'] = '28';
      if (activeDate.getFullYear() % 4 == 0) {
        // Check if the Year is leap year
        navDateVal['next']['day'] = '29';
        if (activeDate.getFullYear() % 100 == 0) {
          if (!activeDate.getFullYear() % 400 == 0) {
            // Special Rules if Leap Year
            navDateVal['next']['day'] = '28';
          }
        }
      }
    }
  } else if (
    (activeDate.getMonth() + 1) == 3 ||
    (activeDate.getMonth() + 1) == 5 ||
    (activeDate.getMonth() + 1) == 8 ||
    (activeDate.getMonth() + 1) == 10
  ) {
    navDateVal['next']['year'] = activeDate.getFullYear();
    navDateVal['next']['month'] = (activeDate.getMonth() + 2);
    navDateVal['next']['day'] = activeDate.getDate();
    if (activeDate.getDate() > 30) {
      // Set to the Maximum Value of 30 Days Months
      navDateVal['next']['day'] = '30';
    }
  } else {
    // Default Choice: Just Carry Over the Date and Select Next Month
    navDateVal['next']['year'] = activeDate.getFullYear();
    navDateVal['next']['month'] = (activeDate.getMonth() + 2);
    navDateVal['next']['day'] = activeDate.getDate();
  }

  calendarNav["next"].setAttribute("data-content",
    navDateVal['next']['year'] + '-' + navDateVal['next']['month'] + '-' + navDateVal['next']['day']
  );
}

function setDropdownNavValues(activeDate) {
  var activeDate = new Date(activeDate);
  let calendarNav = {
    month: document.querySelector('#datepicker-modal .content-nav select[name="month"]'),
    year : document.querySelector('#datepicker-modal .content-nav select[name="year"]')
  };

  // Month Select Node
  var monthNodes = calendarNav['month'].querySelectorAll('option');
  for (var i = 1 ; i <= 12 ; i++) {
    var dayVal;
    if (i == 2) {
      // Detect if the Month is February
      dayVal = activeDate.getDate();
      if (activeDate.getDate() > 28) {
        // Set to the Max Day of Feb (28)
        dayVal = '28';
        if (activeDate.getFullYear() % 4 == 0) {
          // Check if the Year is leap year
          dayVal = '29';
          if (activeDate.getFullYear() % 100 == 0) {
            if (!activeDate.getFullYear() % 400 == 0) {
              // Special Rules if Leap Year
              dayVal = '28';
            }
          }
        }
      }
    } else if (i == 4 || i == 6 || i == 9 || i == 11) {
      dayVal = activeDate.getDate();
      if (activeDate.getDate() > 30) {
        dayVal = '30';
      }
    } else {
      dayVal = activeDate.getDate();
    }
    let dateVal = activeDate.getFullYear() + '-' + i + '-' + dayVal;
    monthNodes[(i - 1)].setAttribute('value', dateVal);

    // Set selected Node
    if (activeDate.getMonth() == (i - 1)) {
      calendarNav['month'].selectedIndex = activeDate.getMonth();
    }
  }

  // Year Select Node
  calendarNav['year'].innerHTML = '';
  // Flag for the Index Selector
  var indexCount = 0;
  var currentDate = new Date();
  for (var i = currentDate.getFullYear() ; i >= 1900 ; i--) {
    let optionNode = document.createElement('option');
    optionNode.textContent = i;
    
    var dayVal;
    if (activeDate.getMonth() == 1) {
      // Detect if the Month is February
      dayVal = activeDate.getDate();
      if (activeDate.getDate() > 28) {
        // Set to the Max Day of Feb (28)
        dayVal = '28';
        if (i % 4 == 0) {
          // Check if the Year is leap year
          dayVal = '29';
          if (i % 100 == 0) {
            if (!i % 400 == 0) {
              // Special Rules if Leap Year
              dayVal = '28';
            }
          }
        }
      }
    } else if (
      activeDate.getMonth() == 4 || 
      activeDate.getMonth() == 6 || 
      activeDate.getMonth() == 9 || 
      activeDate.getMonth() == 11
    ) {
      dayVal = activeDate.getDate();
      if (activeDate.getDate() > 30) {
        dayVal = '30';
      }
    } else {
      dayVal = activeDate.getDate();
    }
    var dateVal = i + '-' + (activeDate.getMonth() + 1) + '-' + dayVal;
    optionNode.setAttribute('value', dateVal);
    calendarNav['year'].appendChild(optionNode);

    // Set selected Node
    if (activeDate.getFullYear() == i) {
      calendarNav['year'].selectedIndex = indexCount;
    }
    // Increase Index count
    indexCount++;
  }
}

$(document).ready(function() {
  // Select Date Event Trigger
  $(document).on('click', '[data-target="#datepicker-modal"]', function(){
    // Get the value of Birthdate Input
    var activeDate = (document.querySelector('[name="birthDate"]')).value;
    console.log(activeDate);

    // Check if the Value of the Birthdate Input is empty
    if (activeDate == '') {
      // Set the active date to current date
      activeDate = new Date();
    }
    updateCaledar(activeDate);
  });

  // Calendar Navigation Button and Fields
  $(document).on('click', '#datepicker-modal .content-nav .btn-prev, #datepicker-modal .content-nav .btn-next', function() {
    let activeDate = $(this).attr('data-content');
    console.log(activeDate);
    updateCaledar(activeDate);
  });
  $(document).on('change', '#datepicker-modal .content-nav [name="month"], #datepicker-modal .content-nav [name="year"]', function() {
    let activeDate = $(this).val();
    console.log(activeDate);
    updateCaledar(activeDate);
  });

  // Date Node Event Listener
  $(document).on('click', '.date-node', function() {
    // Reset Active Date at Calendar Body
    const activeDateNode = $(this)[0];
    let dateNodes = document.querySelectorAll('#datepicker-modal .calendar-body .date-node');
    for (var i = 0 ; i < dateNodes.length ; i++) {
      dateNodes[i].classList.remove('active');
    }
    activeDateNode.classList.add('active');

    // Initialize Arrow Navigation Values 
    console.log(activeDateNode.getAttribute('data-content'));
    setArrowNavValues(activeDateNode.getAttribute('data-content'));

    // Initialize Dropdown Navigation Values
    setDropdownNavValues(activeDateNode.getAttribute('data-content'));

  });

  // Select Date Button 
  $(document).on('click', '.select-date', function() {
    var selectedDate = $('#datepicker-modal .calendar-body .date-node.active').attr('data-content');
    if (!(selectedDate)) {
      // if the Selected Date doesn't have value select the current date
      selectedDate = new Date();
    }

    var dateInputNodes = {
      front : document.querySelector('[data-target="#datepicker-modal"]'),
      back : document.querySelector('input[name="birthDate"]')
    };

    if (selectedDate == '') {
      // Clear the Front Input Value
      let textElem = document.createElement('span');
      textElem.style.color = '#868E96';
      let textContent = document.createTextNode('Select Date');
      textElem.appendChild(textContent);
      dateInputNodes["front"].innerHTML = '';
      dateInputNodes["front"].appendChild(textElem);
    } else {
      // Month & Day Array
      let monthNames = [
        "Jan", "Feb", "Mar", "Apr",
        "May", "Jun", "Jul", "Aug",
        "Sep", "Oct", "Nov", "Dec"
      ];
      let dayNames = [
        "Sunday", "Monday", "Tuesday",
        "Wednesday", "Thursday", "Friday",
        "Saturday"
      ];

      var date = new Date(selectedDate);
      let textContent = document.createTextNode(dayNames[date.getDay()] + ', ' + monthNames[date.getMonth()] + ' ' + date.getDate() + ' ' + date.getFullYear());

      dateInputNodes["front"].innerHTML = '';
      dateInputNodes["front"].appendChild(textContent);
    }

    // Set the Back Input Value
    dateInputNodes["back"].value = selectedDate;

    console.log(dateInputNodes["back"]);
  });
});