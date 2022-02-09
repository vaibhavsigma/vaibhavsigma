function doPost(e) {
 try{
   const folderId = "184vtqwDS_bnuOLzE_HuLAhMDLKFD2YiM";
    const blob = Utilities.newBlob(JSON.parse(e.postData.contents), e.parameter.mimeType, e.parameter.filename);
    var folder = DriveApp.getFolderById('184vtqwDS_bnuOLzE_HuLAhMDLKFD2YiM');
    const file = folder.createFile(blob);
    var data = list_all_files_inside_one_folder_without_subfolders(folderId);
   const responseObj = { code: 'sucess', filename: file.getName(), fileId: file.getId(), dir: data, fileUrl: file.getUrl(), info: "yes"};
    return ContentService.createTextOutput(JSON.stringify(responseObj)).setMimeType(ContentService.MimeType.JSON);
 } catch(err) {
    const responseObj = { code: 'danger', msg: 'Error: '+err.message, error: err, sendData: e };
    return ContentService.createTextOutput(JSON.stringify(responseObj)).setMimeType(ContentService.MimeType.JSON);
  }
}

function list_all_files_inside_one_folder_without_subfolders(folderId){
  var sh = SpreadsheetApp.getActiveSheet();
  var folder = DriveApp.getFolderById('184vtqwDS_bnuOLzE_HuLAhMDLKFD2YiM'); // I change the folder ID  here 
  var list = [];
  list.push(['Name','ID','Size']);
  var files = folder.getFiles();
  while (files.hasNext()){
    file = files.next();
    var row = []
    row.push(file.getName(),file.getId(),file.getSize())
    list.push(row);
  }
  return list;
  //return sh.getRange(1,1,list.length,list[0].length).setValues(list);
}

    const form = document.getElementById('form');
    const processElement = document.getElementById('alert-field') ;
    form.addEventListener('submit', e => {
      e.preventDefault();      
      if( !processElement.classList.contains('in-process') ){
        processElement.innerHTML = '<progress></progress>';
        processElement.removeAttribute("class");
        processElement.setAttribute("class", "in-process"); 
        // $('#alert-field').removeClass().html('<progress></progress>').removeClass('hidden');
        const file = form.file.files[0];
        const fr = new FileReader();
        fr.readAsArrayBuffer(file);
        fr.onload = f => {
          // I added below script.
          let newName = form.filename.value;
          const orgName = file.name;
          if (orgName.includes(".")) {
            const orgExt = orgName.split(".").pop();
            if (orgExt != newName.split(".").pop()) {
              newName = newName ? `${newName}.${orgExt}` : orgName;
            }
          }
          
          const url = "https://script.google.com/macros/s/AKfycbwdvAGmfWCO2z-g9IIYNDljCQ9a-7uvFEtws4CHouGjbOSod6g/exec";
          let header = new Headers({
              'Access-Control-Allow-Origin':'*',
              'Content-Type': 'multipart/form-data'
          });
          
          const qs = new URLSearchParams({filename: newName, mimeType: file.type});  // Modified
          fetch(`${url}?${qs}`, {method: "POST", mode: 'cors', header: header, body: JSON.stringify([...new Int8Array(f.target.result)])})
          .then(res => res.json())
          .then(e => { // <--- You can retrieve the returned value here.
            // $('#alert-field').removeClass().addClass(`alert alert-${e.code}`).html(data.filename+'<br> FileURL: '+e.fileUrl);
            console.log('SUCESS: ',e, e.fileUrl);
            processElement.removeAttribute("class");
            processElement.setAttribute("class", "alert alert-success alert-dismissible"); 
            processElement.innerHTML = 'File Uploaded <br> FilePath: '+e.filePath;
            // $('#alert-field').removeClass().addClass(`alert alert-${data.response.code}`).html(data.response.msg+'<br> FilePath: '+data.response.filePath);
            showFileList();
          })  // <--- You can retrieve the returned value here.
          .catch(err => {
            // $('#alert-field').removeClass().addClass(`alert alert-danger`).text('Somthing went wrong!');
            processElement.removeAttribute("class");
            processElement.setAttribute("class", "alert alert-danger alert-dismissible");
            processElement.innerHTML = 'Somthing went wrong';
            console.log('ERROR  : ',err);
          });
        }
      } else {
        console.log("alredy request in process, so please later!");
      }
    });
    
    function showFileList(){
      if( !processElement.classList.contains('in-process') ){  
        processElement.innerHTML = '<progress></progress>';
        processElement.removeAttribute("class");
        processElement.setAttribute("class", "in-process"); 
        var tbodyRef = document.getElementById('showfiletable').getElementsByTagName('tbody')[0];
        tbodyRef.innerHTML = '<tr><td colspan="4">Please Wait...</td></tr>';
        const url = "https://script.google.com/macros/s/AKfycbwWMnRJRhZNtVsWBvLdzzAE5R0tWINvgFxCtuulyNfSxBcHMeC5/exec";
        let header = new Headers({
            'Access-Control-Allow-Origin':'*',
            'Content-Type': 'multipart/form-data'
        });
       const qs = new URLSearchParams({folderPath: "184vtqwDS_bnuOLzE_HuLAhMDLKFD2YiM"});  // Modified
       fetch(`${url}?${qs}`, {method: "POST", mode: 'cors', header: header, body: JSON.stringify([...new Int8Array(qs)])})
        .then(res => res.json())
        .then(e => { // <--- You can retrieve the returned value here.
          
          var data="";
          processElement.removeAttribute("class");
          for (var i = 1; i <= e.length; i++) {
            // console.log(i,e.length,e[i][0]);
            if(e[i]){ 
              data += "<tr><td>"+e[i][0]+"</td><td>"+e[i][1]+"</td><td>"+e[i][2]+"</td><td>"+e[i][3]+"</td></tr>";
            }
          }
          tbodyRef.innerHTML = data;
          processElement.removeAttribute("class");
          processElement.setAttribute("class", "alert alert-success alert-dismissible"); 
          processElement.innerHTML = 'Request fetch Sucessfully';
          // $('#alert-field').removeClass().addClass(`alert alert-${e.code}`).html(data.filename+'<br> FileURL: '+e.fileUrl);
          console.log('SUCESS: ',e);
        })  // <--- You can retrieve the returned value here.
        .catch(err => {
          // $('#alert-field').removeClass().addClass(`alert alert-danger`).text('Somthing went wrong!');
          processElement.removeAttribute("class");
          processElement.setAttribute("class", "alert alert-danger alert-dismissible");
          processElement.innerHTML = 'Somthing went wrong';
          tbodyRef.innerHTML = '<tr><td colspan="4">Somthing went wrong</td></tr>';
          console.log('ERROR  : ',err);
        });
      } else {
        console.log("alredy request in process, so please later!");
      }
    }
    showFileList();
    </script>
