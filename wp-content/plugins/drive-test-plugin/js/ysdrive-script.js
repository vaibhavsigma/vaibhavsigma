jQuery(document).ready( function( $ ) {
  console.log('openingw127');
  /* media opener script */
  var mediaUploader;
  $('.drive_upload_image_button').on('click',function(e) {
    e.preventDefault();
    if( mediaUploader ){
        mediaUploader.open();
            return; 
    }
    var cuuele = $(this);
    mediaUploader = wp.media.frames.fle_frame = wp.media({
        title: 'Select File To Upload on Google Drive',
        button: {
            text: 'Select File'
        },
        multiple: false
    })

    mediaUploader.on('select', function(){
        attachment = mediaUploader.state().get('selection').first().toJSON();
        console.log('attracty: ',attachment);
        cuuele.siblings('a').attr('href',attachment.url).attr('mime',attachment.mime).attr('name',attachment.name).text(attachment.filename); //get file Name.
        // callimng image preview
    });
    mediaUploader.open(); //open Wp media.
  });       

  /* file size */
  function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
  }

  /* Drive connect script*/
  $('#connect-drive').on('submit', function(e) {
    e.preventDefault();
    console.log('Data URL: ',$('#drive-apikeys').val() );
    let url = $('#drive-apikeys').val();
    if(url == ''){
        alert('Please Enter Google Script API URL for Furter Process!');
        return false;
    }
    let oprationData = { opration: 'code-25-7' };
    if( $('#folder-Id').val() != '' ){
      oprationData = { opration: 'code-25-7', folderID: $('#folder-Id').val() };
    } 
    $.ajax({
      url: url, 
      type: 'POST',
      crossDomain: true,
      data: oprationData,
      success: function(result){
        if( result[0].Status && result[0].Status == 200 ){
          $('#folder-Id').val(result[0].folderID);
          console.log( 'show: ',$('#connect-drive').html() );
          $('#connect-drive').unbind('submit').submit();
        } else {
          console.log('  new:',result[0].msg ,result[0].Status);
          $('.error-notic p').html(result[0].msg);
          $('.error-notic').removeClass('drive-bridge-notic');
        }        
      }, error: function(xhr,status,error){        
        let Error= [{Error: error, Status: status,XHR: xhr }];
        $('.error-notic p').html('Error occure while calling this request connection with given url.\n For more info look into console');
        console.log('Error occure while calling this request.',Error);
        $('.error-notic').removeClass('drive-bridge-notic');
      }
    });
  });

  function updatePostID(theId, folderID){
    $.get('../wp-json/drive-bridge/set-id/?post-id='+theId+'&folder-id='+folderID, function(data, status){
      if( status == 'success' && data.status == 200 ){
        location.reload();
      } else {
        alert(" Error occured while connecting the post please contact devloper for more help or try again later.");
        console.log('Error Data: ',data,'\n Error Status: ',status)
      }
    });  
  }

  function updatePostChecklistID(theId, sheetID){
    $.get('../wp-json/drive-bridge/set-id/?post-id='+theId+'&checklist-id='+sheetID, function(data, status){
      if( status == 'success' && data.status == 200 ){
        location.reload();
      } else {
        alert(" Error occured while connecting the post please contact devloper for more help or try again later.");
        console.log('Error Data: ',data,'\n Error Status: ',status)
      }
    });  
  }

  // create folder for project in drive to store project data
  jQuery('#connect-post-to-drive').click(function (e){
    console.log('post to drive is working...');
    if( jQuery('.posts-folder-inner').val() != '' ){
      let current = $(this);
      current.siblings('.ysdrive-spinner').show();
      current.hide();
      oprationData = { opration: 'code-28-11', parentFolderId: $('#folder-id-inner').val(), folderName: $('#posts-folder-inner').attr('post-title') };
      $.ajax({
        url: jQuery('#api-key-inner').val(), 
        type: 'POST',
        crossDomain: true,
        header : {
            "accept": "application/json",
            "Access-Control-Allow-Origin":"*"
        },
        data: oprationData,
        success: function(result){
          if( result[0].Status && result[0].Status == 200 && result[0].newfolderID && result[0].newfolderID != ''){
            updatePostID($('#posts-folder-inner').attr('posts-id'), result[0].newfolderID );
          } else {
            current.siblings('.ysdrive-spinner').hide();
            current.show();  
            console.log('  new:',result[0].msg ,result[0].Status);
            $('.error-notic p').html(result[0].msg);
            $('.error-notic').removeClass('drive-bridge-notic');
          }
          console.log('  result: ',result);        
          
        }, error: function(xhr,status,error){  
          current.siblings('.ysdrive-spinner').hide();
          current.show();        
          let Error= [{Error: error, Status: status,XHR: xhr }];
          $('.error-notic p').html('Error occure while calling this request connection with given url.\n For more info look into console');
          console.log('Error occure while calling this request.',Error);
          $('.error-notic').removeClass('drive-bridge-notic');
        }
      });
    } 
  });
  // create checklist for project in drive as spreedsheet
  jQuery('#create-check-in-drive').click(function (e){
    console.log('post to drive is working...');
    if( jQuery('.posts-folder-inner').val() != '' ){
      let current = $(this);
      current.siblings('.ysdrive-spinner').show();
      current.hide();
      oprationData = { opration: 'code-09-96', parentFolderId: $('#posts-folder-inner').val(), folderName: $('#posts-folder-inner').attr('post-title') };
      $.ajax({
        url: jQuery('#api-key-inner').val(), 
        type: 'POST',
        crossDomain: true,
        header : {
            "accept": "application/json",
            "Access-Control-Allow-Origin":"*"
        },
        data: oprationData,
        success: function(result){
          if( result[0].Status && result[0].Status == 200 && result[0].newsheetID && result[0].newsheetID != ''){
            updatePostChecklistID($('#posts-folder-inner').attr('posts-id'), result[0].newsheetID );
          } else {
            current.siblings('.ysdrive-spinner').hide();
            current.show();
            console.log('  new:',result[0].msg ,result[0].Status);
            $('.error-notic p').html(result[0].msg);
            $('.error-notic').removeClass('drive-bridge-notic');
          }  
          console.log('  result: ',result);        
        }, error: function(xhr,status,error){      
          current.siblings('.ysdrive-spinner').hide();
          current.show();  
          let Error= [{Error: error, Status: status,XHR: xhr }];
          $('.error-notic p').html('Error occure while calling this request connection with given url.\n For more info look into console');
          console.log('Error occure while calling this request.',Error);
          $('.error-notic').removeClass('drive-bridge-notic');
        }
      });
    } 
  });
  /* call google scritp for getting data */
  var processElement = false;
  function showFileList(){
    if(processElement == false)
    if(jQuery('#api-key-inner').val() !='' && jQuery('#folder-id-inner').val() != ''){
      if( jQuery('#posts-folder-inner').val() && jQuery('#posts-folder-inner').val() != '' ){
        console.log("process is starting: ");
        processElement == true;
        oprationData = { opration: 'code-9-2', folderID: jQuery('#posts-folder-inner').val() };
        console.log('calling ajax');  
        $.ajax({
          url: jQuery('#api-key-inner').val(), 
          type: 'POST',
          crossDomain: true,
          data: oprationData,
          success: function(result){
            if( result[0].Status && result[0].Status == 200 && result[0].docList.length > 1 ){
              var data = '';
              for (var i = 1; i <= result[0].docList.length; i++) {
                if(result[0].docList[i]){ 
                  data += "<tr><td>"+result[0].docList[i][0]+"</td><td>"+formatBytes(result[0].docList[i][2])+"</td><td><a href=\""+result[0].docList[i][3]+"\" target=\"_blank\">"+result[0].docList[i][3]+"</a></td></tr>";
                }
              }
              jQuery('#showfiletable tbody').html(data);
            } else {
              jQuery('#showfiletable tbody').html("<tr><td colspan='4'>"+result[0].msg+"</td></tr>");
            }     
          }, error: function(xhr,status,error){        
            processElement = false;
            let Error= [{Error: error, Status: status,XHR: xhr }];
            alert('Error occure while calling this request connection with given url.\n For more info look into console');
            console.log('Error occure while calling this request.',Error);
          }
        });
      } 
    }
  } 
  showFileList();
  var processFile = true;
  jQuery("#upload-file-to-drive").on('click', function(){
    let fileURL = jQuery("#file-sec-drive").siblings('a').attr('href');
    if(processFile == true){
      if( fileURL && fileURL != '' ){
        processFile = false;
        fetch(fileURL)
        .then(response => response.text())
        .then(data => {
          // Do something with your data
          let mainattr = jQuery('#file-sec-drive').siblings('a');
          let filename = mainattr.attr('name');
          if( jQuery('#drive-file-name').val() || jQuery('#drive-file-name').val() != '' ){
            filename = jQuery('#drive-file-name').val();
          }
          var fileData = { opration: 'code-07-98', url: mainattr.attr('href'),mime: mainattr.attr('mime'),name: filename, folderId: jQuery('#posts-folder-inner').val() };
          console.log(fileData);
          $.ajax({
            url: jQuery('#api-key-inner').val(), 
            type: 'POST',
            crossDomain: true,
            data: fileData,
            success: function(result){
              processFile = true;
              if(result[0].Status && result[0].Status == 200 ){
                showFileList();
                alert('File Uploaded Sucessfully.');
                console.log('File Uploaded Sucessfully..',result);
                $('#drive-file-name').val('');
                $('#file-sec-drive').siblings('a').remove();
              } else {
                alert('Error occure while uploading document.\n For more info look into console');
                console.log('Error occure while calling this request.\n',Error);
              }
            }, error: function(xhr,status,error){        
              processFile = true;
              let Error= [{Error: error, Status: status,XHR: xhr }];
              alert('Error occure while calling this request connection with given url.\n For more info look into console');
              console.log('Error occure while calling this request.',Error);
            }
          });
        }).catch(function(err) {
          processFile = true;
          alert('somthing went wrong please try again later.');
          console.log('Error: ',err);
        });
      } else {
        alert('Please select file to upload.');
        console.log('nothing to show from my end',fileURL);
      }
    }  else {
      alert('Your Prevoius request in process please wait...');
    }
  }); 

  var $countLi = $('.check-list').find('li').length?$('.check-list').find('li').length + 1 :1;
  $('.ysdrive-add-list').click( function(e) { 
    e.preventDefault();
    if($('.check-list').find('li').length > 49 ) {
      alert('You Have Reich The Limit for list');
    } else { 
      $data = '<li class="listitems"><label for="list-item-'+ $countLi+'">No. '+formatNum( $('.check-list').find('li').length + 1 )+' <span>:</span></label><a class="button button-primary ysdrive-remove-list" item="item-'+$countLi+'">Remove</a><input type="text" id="list-item-'+$countLi+'"  name="item-'+$countLi+'" required /></li>';
      $('.check-list').append($data);
      $countLi++;
    }
  });
	
	$(document).on('click', '.ysdrive-remove-list', function(e) { 
		e.preventDefault();
    $(this).parents('li.listitems').remove();
    listNumOrder();
	});

  function listNumOrder(){
    $('ul.check-list li.listitems').each( function(indexInArray, valueOfElement) { 
       $(valueOfElement).find('label').html('No. '+formatNum(indexInArray + 1)+' <span>:</span>');
    });
  }
  var processlistUP = false;
  $('form.list-form').submit( function(e) { 
    let reponseEle = $('h6.reponse-msg');
    reponseEle.text('');
    reponseEle.removeClass('success error');
    e.preventDefault();
    if( processlistUP != false ){
      reponseEle.text('Please wait for previous request to complate.');
      reponseEle.addClass('error');
      return false;
    }    
    if($('.check-list').find('li').length < 1 ) {
      reponseEle.text('Please add check list points to Update.');
      reponseEle.addClass('error');
    } else { 
      processlistUP = true;
      $('.ysdrive-add-list, .ysdrive-update-list').hide();
      $.ajax({
        url: jQuery('#ajax-url').val(), 
        type: 'POST',
        crossDomain: true,
        data: $(this).serialize() ,
        success: function(result){          
          processlistUP = false; 
          reponseEle.text(result);
          reponseEle.addClass('success');
          $('.ysdrive-add-list, .ysdrive-update-list').show();
        }, error: function(xhr,status,error){        
          $('.ysdrive-add-list, .ysdrive-update-list').show();
          processlistUP = false;
          reponseEle.addClass('error');
          reponseEle.text('Server error occure while calling this request.');
          console.log('Error occure while calling this request.',Error);
        }
      });
    }
  });
  var processChecklist = true;
  $('button.upload-checklist-to-drive').click( function(e) { 
    e.preventDefault();
    if(processChecklist == true){
      var $listitem = $('li > input[type="checkbox"]:checked');
      if( $listitem && $listitem.length ){
        processChecklist = false;
        var checkarray = [];
        $listitem.each(function() {
          checkarray.push($(this).val());
        });
        var fileData = { opration: 'code-09-77', data: checkarray.join(), folderId: jQuery('#posts-folder-inner').val() };
        console.log(fileData);
        $.ajax({
          url: jQuery('#api-key-inner').val(), 
          type: 'POST',
          crossDomain: true,
          data: fileData,
          success: function(result){
            processChecklist = true;
            if(result[0].Status && result[0].Status == 200 ){
              alert(result.msg);
              console.log('check List Add Sucessfully..',result);
            } else {
              alert(result.msg);
              console.log('Error occure while calling this request.\n',result);
            }
          }, error: function(xhr,status,error){        
            processChecklist = true;
            let Error= [{Error: error, Status: status,XHR: xhr }];
            alert('Error occure while calling this request connection with given url.\n For more info look into console');
            console.log('Error occure while calling this request.',Error);
          }
        });
      } else {
        alert('Please select checklist points to save.');
      }
    }  else {
      alert('Your Prevoius request in process please wait...');
    }
  });

  function showCheckList(){ 
    if(processChecklist == true){
      var $listitem = $('#manage-checklist').find('li input[type="checkbox"]');
      if( $listitem && $listitem.length ){
        $('#manage-checklist').find('ul').hide();
        processChecklist = false;
        let fileData = { opration: 'code-09-79', folderId: jQuery('#posts-folder-inner').val() };
        $.ajax({
          url: jQuery('#api-key-inner').val(), 
          type: 'POST',
          crossDomain: true,
          data: fileData,
          success: function(result){
            $('#manage-checklist').find('ul').show();
            processChecklist = true;
            if(result[0].Status && result[0].Status == 200 ){
              console.log('check List Add Sucessfully..',result);
              if(result[0].filecontent && result[0].filecontent != ''){
                $split = result[0].filecontent.split(',');
                $.each($split, function(index, val) {
                  jQuery('input[type="checkbox"][value="'+val+'"]').prop('checked', true);
                });
              }
            } else {
              console.log('Error occure while calling this request.\n',result);
            }
          }, error: function(ts){        
            processChecklist = true;
            alert('Error occure while calling this request connection with given url.\n For more info look into console');
            console.log('Error occure while calling this request.',ts);
          }
        });
      }
    }  else {
      alert('Your Prevoius request in process please wait...');
    }
  }
  showCheckList();

  $('a#ysdrive-content').click(function (e) { 
    e.preventDefault();
    let contentBtn = $(this);
    if( $('textarea[name="content"]').val().length && $('textarea[name="content"]').val() != ''){
      contentBtn.hide();
      contentBtn.siblings('.ysdrive-spinner').show();
      let fileData = { opration: 'code-09-80', content: $('textarea[name="content"]').val(), folderId: jQuery('#posts-folder-inner').val() };
      $.ajax({
        url: jQuery('#api-key-inner').val(), 
        type: 'POST',
        crossDomain: true,
        data: fileData,
        success: function(result){
          contentBtn.show();
          contentBtn.siblings('.ysdrive-spinner').hide();
          if(result[0].Status && result[0].Status == 200 ){
            console.log('Project content uploaded Sucessfully..',result);
            alert('Project content uploaded Sucessfully.');
          } else {
            console.log('Error occure while uploading Project Content this request.\n',result);
          }
        }, error: function(ts){  
          contentBtn.show();
          contentBtn.siblings('.ysdrive-spinner').hide();      
          alert('Error occure while calling this request connection with given url.\n For more info look into console');
          console.log('Error occure while calling this request.',ts);
        }
      });
    } else {
      alert('Please add content to upload data');
    }
  });
  
  $(".nav-tab-wrapper a.nav-tab").click(function (e) { 
    e.preventDefault();
    $(".nav-tab").removeClass("nav-tab-active");
    $(this).addClass("nav-tab-active");
    var cur_content = $(this).attr("ysdrive_currtab");
    $(".tab-content").hide();
    $(`#${cur_content}`).show();
  });
  $(".nav-tab-wrapper a.nav-tab").eq(0).click();
});

function formatNum(num){ return num > 9 ? "" + num: "0" + num; }