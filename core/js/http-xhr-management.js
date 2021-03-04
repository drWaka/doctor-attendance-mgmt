function sendXHR(url, method, data, successFunction) {
    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function (result) {
          console.log(result);
          result = JSON.parse(result);
          if (result['success'] != 'success') initializeModal(result['content'].modal);
          
          successFunction(result);
        },
        error: function() {
            console.log('Error Encountered');
        }
    });
  }