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