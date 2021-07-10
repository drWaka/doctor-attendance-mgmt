function sendXHR(url, method, data, successFunction, originNode = "", failCallback = "") {
  failCallback = failCallback || (() => {console.log("Error Encounter");});

  $.ajax({
    url: url,
    method: method,
    data: data,
    success: function (result) {
      setTimeout(() => { manageLoading("hide"); }, 300);

      // {   Result Structure
      //     httpStatus : '',
      //     type: 'modal, notif, element',
      //     level: 'warning, danger, success, info',
      //     content: 'rawText'
      // }
      console.log(result);
      result = JSON.parse(result);
      console.log(result);
      if (result["httpStatus"] == "success") {
        successFunction(result, originNode);
      } else {
        if (result["type"] == "modal") {
          // Call Modal Initialize Function
          initializeModal(result["content"]);
        } else {
          const header = {
            warning: "Warning",
            danger: "Error",
            success: "Success",
            info: "Notice",
          };
          NotificationManagement.put({
            header: header[result["level"]],
            message: result["content"],
            type: result["level"],
          });
        }
      }
    },
    beforeSend: function () {
      manageLoading("show");
    },
    error: function (result) {
      setTimeout(() => { manageLoading("hide"); }, 300);
      failCallback(result);
    },
  });
}

// Generic HTTP Callback Functions
let genericSuccessFunc = (result, destinationNode) => {
  destinationNode.innerHTML = result["content"];
};
