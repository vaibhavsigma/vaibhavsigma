function doPost(e) {
  try{
    var op = e.parameter.action;

    var ss=SpreadsheetApp.openByUrl("https://docs.google.com/spreadsheets/d/1t4TT3-7DiHtljoJTUubhzeyy6J0uErI0uGlEolqwcPA/edit#gid=0");
    var sheet = ss.getSheetByName("Sheet1");
    var result;
     if(op=="insert")
      result = insert_value(e,sheet);
    
    //Make sure you are sending proper parameters 
    if(op=="read")
      result = read_value(e,ss);
    
    const responseObj = { code: 'sucess', result: result };
    return ContentService.createTextOutput(JSON.stringify(responseObj)).setMimeType(ContentService.MimeType.JSON);
  } catch(err) {
    const responseObj = { code: 'danger', msg: 'Error: '+err.message, error: err, sendData: e };
    return ContentService.createTextOutput(JSON.stringify(responseObj)).setMimeType(ContentService.MimeType.JSON);
  }
}

function insert_value(request,sheet){
 
  try{
    var id = request.parameter.id;
    var username = request.parameter.username;
    var password = request.parameter.password;
    
    var flag=1;
    var lr= sheet.getLastRow();
    for(var i=1;i<=lr;i++){
      var id1 = sheet.getRange(i, 2).getValue();
      if(id1==id){
        flag=0;
    var result="Id already exist..";
      } }
    //add new row with recieved parameter from client
    if(flag==1){
    var d = new Date();
      var currentTime = d.toLocaleString();
    var rowData = sheet.appendRow([currentTime,id,username,password]);  
    var result="Insertion successful";
    } else {
      return update_value(request,sheet);
    }
      
    return result;
  } catch(err) {
    const responseObj = { code: 'danger', msg: 'Error: '+err.message, error: err, sendData: e };
    return ContentService.createTextOutput(JSON.stringify(responseObj)).setMimeType(ContentService.MimeType.JSON);
  }
}
  
  



function read_value(request,ss){
  
 
  var output  = ContentService.createTextOutput(),
      data    = {};
  //Note : here sheet is sheet name , don't get confuse with other operation 
      var sheet="sheet1";

  data.records = readData_(ss, sheet);
  
  var callback = request.parameters.callback;
  
  if (callback === undefined) {
    output.setContent(JSON.stringify(data));
  } else {
    output.setContent(callback + "(" + JSON.stringify(data) + ")");
  }
  output.setMimeType(ContentService.MimeType.JAVASCRIPT);
  
  return output;
}


function readData_(ss, sheetname, properties) {

  if (typeof properties == "undefined") {
    properties = getHeaderRow_(ss, sheetname);
    properties = properties.map(function(p) { return p.replace(/\s+/g, '_'); });
  }
  
  var rows = getDataRows_(ss, sheetname),
      data = [];

  for (var r = 0, l = rows.length; r < l; r++) {
    var row     = rows[r],
        record  = {};

    for (var p in properties) {
      record[properties[p]] = row[p];
    }
    
    data.push(record);

  }
  return data;
}



function getDataRows_(ss, sheetname) {
  var sh = ss.getSheetByName(sheetname);

  return sh.getRange(2, 1, sh.getLastRow() - 1, sh.getLastColumn()).getValues();
}


function getHeaderRow_(ss, sheetname) {
  var sh = ss.getSheetByName(sheetname);

  return sh.getRange(1, 1, 1, sh.getLastColumn()).getValues()[0];  
} 
  

//update function

function update_value(request,sheet){
  try{
    var output  = ContentService.createTextOutput();
    var id = request.parameter.id;
    var flag=0;
    var username = request.parameter.username;
    var password = request.parameter.password;
    var lr= sheet.getLastRow();
    for(var i=1;i<=lr;i++){
      var rid = sheet.getRange(i, 2).getValue();
      if(rid==id){
        sheet.getRange(i,3).setValue(username);
        sheet.getRange(i,4).setValue(password);
        var result="value updated successfully";
        flag=1;
      }
    }
    if(flag==0)
      var result="id not found";
      
    return result;
  } catch(err) {
    const responseObj = { code: 'danger', msg: 'Error: '+err.message, error: err, sendData: e };
    return ContentService.createTextOutput(JSON.stringify(responseObj)).setMimeType(ContentService.MimeType.JSON);
  }  
}



//https://script.google.com/macros/s/AKfycbx-Gb8PMh2KIw8QTFRfOa4ReYeYwRU2XDAupdljjTnrUCguet41/exec