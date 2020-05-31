function changePage(page, container, metaType = '', isCachedRequest = true) {
  // Empty the Container
  $(container).html('');
  
  // Loop Flags
  let loopFlags = {
    start : 0,
    end : 0
  };

  if (isCachedRequest) {
    loopFlags.start = (page - 1) * pageProp['pageLimit'];
    loopFlags.end = (page * pageProp['pageLimit']);
  } else {
    loopFlags.start = 0;
    loopFlags.end = pageProp['pageLimit']
  }

  for (var index = loopFlags.start ; index < loopFlags.end ; index++) {
    (function() {
      if (pageProp['isMultiRec'] == 1) {
        $(container).append(pageProp['recObj'][metaType][index]);
      } else {
        $(container).append(pageProp['recObj'][index]);
      }
    })();
  }

  // Pagination Controls
  var pageCont = document.querySelector(container);
  var pageContTemp = null;
  while (pageContTemp == null) {
    pageContTemp = pageCont.querySelector('.pagination-container');
    if (pageContTemp !== null) {
      pageCont = pageContTemp;
    } else {
      pageCont = pageCont.parentNode;
    }
  }
  var prevBtn = pageCont.querySelector('.prev-btn');
  var nextBtn = pageCont.querySelector('.next-btn');
  
  // Prev Page Management
  if (page <= 1) {
    // Trick to prevent the Ajax request from not submitting
    setTimeout(() => {
      prevBtn.setAttribute('disabled', 'disabled');
    }, 100);
  } else {
    prevBtn.removeAttribute('disabled');
  }

  // Next Page Management
  $pageCiel = pageProp['isMultiRec'] == 1 
    ? Math.ceil(pageProp['totalRec'][metaType] / pageProp['pageLimit'])
    : Math.ceil(pageProp['totalRec'] / pageProp['pageLimit']);

  if (page == $pageCiel) {
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
    let container = '.' + $(this).attr('data-container');

    if (pageProp['isMultiRec'] == 1) {
      // Pagination for Multi Record Pages
      let metaType = $(this).attr('meta-type');

      // Validation
      pageProp['curPage'][metaType] = pageProp['curPage'][metaType] <= 1
        ? 1
        : pageProp['curPage'][metaType] - 1;

      // Update Content
      changePage(pageProp['curPage'][metaType], container, metaType);
    } else {
      // Validation
      pageProp['curPage'] = pageProp['curPage'] <= 1
        ? 1
        : pageProp['curPage'] - 1;

      // Pagination for Single Record Pages
      changePage(pageProp['curPage'], container, '', pageProp['isCached']);
    }
  });

  $('.next-btn').on('click', function() {
    let container = '.' + $(this).attr('data-container');

    if (pageProp['isMultiRec'] == 1) {
      // Pagination for Multi Record Pages
      let metaType = $(this).attr('meta-type');

      // Validation
      pageProp['curPage'][metaType] = pageProp['curPage'][metaType] >= Math.ceil(pageProp['totalRec'][metaType] / pageProp['pageLimit'])
        ? Math.ceil(pageProp['totalRec'][metaType] / pageProp['pageLimit'])
        : pageProp['curPage'][metaType] + 1;

      // Update Content
      changePage(pageProp['curPage'][metaType], container, metaType);
    } else {
      // Validation
      pageProp['curPage'] = pageProp['curPage'] >= Math.ceil(pageProp['totalRec'] / pageProp['pageLimit'])
        ? Math.ceil(pageProp['totalRec'] / pageProp['pageLimit'])
        : pageProp['curPage'] + 1;
      
      // Pagination for Single Record Pages
      changePage(pageProp['curPage'], container, '', pageProp['isCached']);
    }
  });

  // Event for Single AJAX Request per page Pagination
  if (typeof pageProp['isCached'] !== 'undefined') {
    if (pageProp['isCached'] == false) {
      // Refresh Record list upon clicking nav buttons
      $(document).on('click', '.nav-btn', function() {
        setTimeout(refreshList(), 100);
      });
    }
  }
});