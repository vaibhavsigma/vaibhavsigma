jQuery(document).ready(function() {
  jQuery('.form-control').focus(formFocus);

  function formFocus() {
   jQuery('#alert-field')
      .removeClass()
      .addClass('hidden');
  }
  
});

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}



const form = document.getElementById('form');
const logindataform = document.getElementById('sheetUpdate');
const processElement = document.getElementById('alert-field') ;
if( form ){
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
}
  
if( logindataform ){
  logindataform.addEventListener('submit', e => {
    e.preventDefault();      
    if( !processElement.classList.contains('in-process') ){  
      processElement.innerHTML = '<progress></progress>';
      processElement.removeAttribute("class");
      processElement.setAttribute("class", "in-process"); 
      const url = "https://script.google.com/macros/s/AKfycbx-Gb8PMh2KIw8QTFRfOa4ReYeYwRU2XDAupdljjTnrUCguet41/exec";
      let header = new Headers({
          'Access-Control-Allow-Origin':'*',
          'Content-Type': 'multipart/form-data'
      });
      let username = logindataform.username.value;
      let password = logindataform.password.value;
      let action = logindataform.action.value;
      let id = logindataform.id.value;
    const qs = new URLSearchParams({"id": id, 'action': action, 'username': username, 'password': password});  // Modified
    fetch(`${url}?${qs}`, {method: "POST", mode: 'cors', header: header, body: JSON.stringify([...new Int8Array(qs)])})
      .then(res => res.json())
      .then(e => { // <--- You can retrieve the returned value here.
      
        processElement.removeAttribute("class");
        processElement.setAttribute("class", "alert alert-success alert-dismissible"); 
        processElement.innerHTML = 'Request Sucessfully complated <hr>'+e.result;
        // $('#alert-field').removeClass().addClass(`alert alert-${e.code}`).html(data.filename+'<br> FileURL: '+e.fileUrl);
        console.log('SUCESS: ',e);
      })  // <--- You can retrieve the returned value here.
      .catch(err => {
        // $('#alert-field').removeClass().addClass(`alert alert-danger`).text('Somthing went wrong!');
        processElement.removeAttribute("class");
        processElement.setAttribute("class", "alert alert-danger alert-dismissible");
        processElement.innerHTML = 'Somthing went wrong';
        console.log('ERROR  : ',err);
      });
    } else {
      console.log("alredy request in process, so please later!");
    }
  });
}
  

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
          data += "<tr><td>"+e[i][0]+"</td><td>"+e[i][1]+"</td><td>"+formatBytes(e[i][2],2)+"</td><td>"+e[i][3]+"</td></tr>";
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
if( processElement ){
  showFileList();
}
  
// jQuery(document).ready( function(){
//   jQuery()
// });