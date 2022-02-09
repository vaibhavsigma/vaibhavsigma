function doPost(e) {
  var folder = DriveApp.getFolderById('184vtqwDS_bnuOLzE_HuLAhMDLKFD2YiM'); // I change the folder ID  here 
  var list = [];
  list.push(['Name','ID','Size','FileURL']);
  var files = folder.getFiles();
  while (files.hasNext()){
    file = files.next();
    var row = []
    row.push(file.getName(),file.getId(),file.getSize(),file.getUrl())
    list.push(row);
  }
  return ContentService.createTextOutput(JSON.stringify(list)).setMimeType(ContentService.MimeType.JSON);;
}