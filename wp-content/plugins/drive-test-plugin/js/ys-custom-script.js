(function($) {
  
  $(function() {
    $('.form-control').focus(formFocus);
  });

  function formFocus() {
    $('#alert-field')
      .removeClass()
      .addClass('hidden');
  }
  // $('#send-drive-data').ajaxForm(function() { 
  //               alert("Thank you for your comment!"); 
  //           });
  $('#send-drive-data').submit(function(e) {
    e.preventDefault();
    $form = document.getElementById('send-drive-data');
    // var formData = [];//formData.cust = 'saccxsx';
    // for (i = 0; i < 6; i++) {
    //   formData.push($form.elements[i]. $form.elements[i].value);
    //   console.log($form.elements[i].name,$form.elements[i].value);
    // }
    // console.log(formData,$form.length);
    // document.getElementById("demo").innerHTML = txt;

      
    /*
      var url = "https://script.google.com/macros/s/AKfycbwoWYCrV6cavtylMp0lfHVYoZHSB4_OeC439NSovBaW1tlXC3sF/exec?name=";
      var name = "Amit Agarwal"
      $('#alert-field').removeClass().html('<progress></progress>').removeClass('hidden');
      $.ajax({
        crossDomain: true,
        url: url + encodeURIComponent(name),
        method: "GET",
        dataType: "jsonp",
        success: function(data, status, xhr) {
            e.target.reset();
            $('#alert-field')
              .removeClass()
              .addClass(`alert alert-${data}`)
              .text(data);
              console.log("response: ",data);
        },
        error: function(xhr){
          alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
      });


    // print the returned data
    function ctrlq(e) {
      // console.log(e.result)
    }
    */
    var formData = new FormData();
    var file = document.querySelector('#file-field');

    formData.append("file", file.files[0]);
    // formData.append("document", documentJson); instead of this, use the line below.
    formData.append("document", JSON.stringify(file));

    const POST_URL = 'https://script.google.com/macros/s/AKfycbwoWYCrV6cavtylMp0lfHVYoZHSB4_OeC439NSovBaW1tlXC3sF/exec';

    const postRequest = {
      name: e.target['name-field'].value,
      email: e.target['email-field'].value,
      subject: e.target['other-subject-field'].value || e.target['subject-field'].value,
      body: e.target['body-field'].value,
      fileData: formData      
    };

    var crossDomain =  true;
    var formData = new FormData($form);
    var formData = $('#send-drive-data').serialize();
    console.log(formData);
    if(POST_URL) {
       $('#alert-field').removeClass().html('<progress></progress>').removeClass('hidden');
       /*$.ajax({
        type: "GET",
        processData : false,
        cache : false,
        url: POST_URL,
        data:formData,
        dataType: "jsonp",
        async:true,
        crossDomain:true,
        headers : {
            "accept": "application/json",
            "Access-Control-Allow-Origin":"*"
        },
        success: function(data, status, xhr) {
            e.target.reset();
            $('#alert-field').removeClass().addClass(`alert alert-${data.response.code}`).html(data.response.msg+'<br> FilePath: '+data.response.filePath);
            console.log("Data: ",data.response,data.response.msg,data);
            console.log('Send');
        },
        error: function(xhr){
          alert("An error occured: " + xhr.status + " " + xhr.statusText);
          $('#alert-field').removeClass().addClass(`alert alert-danger`).text('Somthing went wrong!');
        }*/
      // });
        var options = { 
          target:        '#output2',   // target element(s) to be updated with server response 
          beforeSubmit:  showRequest,  // pre-submit callback 
          success:       showResponse,  // post-submit callback 
   
          // other available options: 
          url:       POST_URL,         // override for form's 'action' attribute 
          type:      'POST',       // 'get' or 'post', override for form's 'method' attribute 
          dataType:  'json',        // 'xml', 'script', or 'json' (expected server response type) 
          clearForm: true,        // clear all form fields after successful submit 
          resetForm: true        // reset the form after successful submit 
   
          // $.ajax options can be used here too, for example: 
          //timeout:   3000 
        }; 
        $(this).ajaxSubmit(options);
     
      /*$.get(POST_URL, postRequest)
        .then(res => {
          console.log("resiult :",res);
          e.target.reset();
          $('#alert-field')
            .removeClass()
            .addClass(`alert alert-${res.code}`)
            .text(res.msg);
        });

      $('#alert-field')
        .removeClass()
        .html('<progress></progress>')
        .removeClass('hidden');  */
    } else {
      alert('You must set the POST_URL variable with your script ID');
    }

  });
  $('#subject-field-id').on('change', function(e) {
    if(e.target.value === 'Other') {

      $('#subject-select').removeClass('col-xs-12')
        .addClass('col-xs-6');
      $('#hidden-other-subject').removeClass('hidden');
    } else {
      $('#subject-select').removeClass('col-xs-6')
        .addClass('col-xs-12');

      $('#hidden-other-subject').addClass('hidden');
    }
  });

 function showRequest(formData, jqForm, options) { 
    // formData is an array; here we use $.param to convert it to a string to display it 
    // but the form plugin does this for you automatically when it submits the data 
    var queryString = $.param(formData); 
 
    // jqForm is a jQuery object encapsulating the form element.  To access the 
    // DOM element for the form do this: 
    // var formElement = jqForm[0]; 
    // var postRequest = {
    //   name: formData[0].value,
    //   email: formData[1].value,
    //   subject: formData[2].value || formData[3].value,
    //   bodyFile: formData[4].value,
    //   body: formData[5].value
    // };
    // var POST_URL = 'https://script.google.com/macros/s/AKfycbwoWYCrV6cavtylMp0lfHVYoZHSB4_OeC439NSovBaW1tlXC3sF/exec';
    //  $.get(POST_URL, postRequest)
    //     .then(res => {
    //       console.log("resiult :",res);
    //       e.target.reset();
    //       $('#alert-field')
    //         .removeClass()
    //         .addClass(`alert alert-${res.code}`)
    //         .text(res.msg);
    //     });
    alert('About to submit: \n\n' + queryString); 
    console.log('About to submit: \n\n' + queryString); 
    console.log(formData); 
 
    // here we could return false to prevent the form from being submitted; 
    // returning anything other than false will allow the form submit to continue 
    return true; 
} 
 
// post-submit callback 
function showResponse(responseText, statusText, xhr, $form)  { 
    // for normal html responses, the first argument to the success callback 
    // is the XMLHttpRequest object's responseText property 
 
    // if the ajaxSubmit method was passed an Options Object with the dataType 
    // property set to 'xml' then the first argument to the success callback 
    // is the XMLHttpRequest object's responseXML property 
 
    // if the ajaxSubmit method was passed an Options Object with the dataType 
    // property set to 'json' then the first argument to the success callback 
    // is the json data object returned by the server 
 
    alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
        '\n\nThe output div should have already been updated with the responseText.'); 
    console.log('status: ' + statusText + ' \n\nresponseText: \n' + responseText + 
        ' \n\nThe output div should have already been updated with the responseText.'); 
} 

  
})( jQuery );
