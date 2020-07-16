function manageNav() {
  var prevBtn = document.querySelector('.prev-btn');
  var nextBtn = document.querySelector('.next-btn');
  
  // Prev Page Management
  if (pageConfig['page'] <= 1) {
    // Trick to prevent the Ajax request from not submitting
    setTimeout(() => {
      prevBtn.setAttribute('disabled', 'disabled');
    }, 100);
  } else {
    prevBtn.removeAttribute('disabled');
  }

  // Next Page Management
  $maxPage = Math.ceil(pageConfig['total'] / pageConfig['limit']);

  if (pageConfig['page'] == $maxPage) {
    // Trick to prevent the Ajax request from not submitting
    setTimeout(() => {
      nextBtn.setAttribute('disabled', 'disabled');
    }, 100);
  } else {
    nextBtn.removeAttribute('disabled');
  }  
}

$(document).ready(function() {
  // Pagination Button Events
  $('.prev-btn').on('click', function() {
    if (pageConfig['page'] > 1) {
      pageConfig['page']--;
    }
    loadRecord();
  });

  $('.next-btn').on('click', function() {
    $maxPage = Math.ceil(pageConfig['total'] / pageConfig['limit']);
    if (pageConfig['page'] < $maxPage) {
      pageConfig['page']++;
    }
    loadRecord();
  });
});