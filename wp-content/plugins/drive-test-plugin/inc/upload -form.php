<?php /*/-----> Uplaod form <-----/*/?>
  <style type="text/css">
      .container{
          margin-top: 20px;
      }
      .container > progress {
          width: 100%;
      }         
      .container > input {
          transition: all 0.5s
      }         
  </style>
  <div class="container">
    <form id="send-drive-data" action="https://script.google.com/macros/s/AKfycbwoWYCrV6cavtylMp0lfHVYoZHSB4_OeC439NSovBaW1tlXC3sF/exec" method="POST" enctype="multipart/form-data">
      <legend>Contact Form</legend>
      <div id="alert-field" class="alert hidden">
        <p>Uh oh! Something went wrong!</p>
      </div>    
      <div class="form-group col-xs-6">
        <label for="name-field">Name</label>
        <input type="text" class="form-control" id="name-field" name="name" placeholder="Your name" required>
      </div>
      
      <div class="form-group col-xs-6">
        <label for="email-field">Email</label>
        <input type="email" class="form-control" id="email-field" name="email-field" placeholder="Email address" required>
      </div>
      
      <div id="subject-select" class="form-group col-xs-12">
        <label for="subject-field">Subject</label>
        <select class="form-control" id="subject-field-id" name="subject-field" required>
          <option value="Consulting">#EdTech Consulting</option>
          <option value="Web Development">Web Development projects</option>
          <option value="Google Scripts">Google Apps Scripts</option>
          <option value="G Suite for Education">G Suite for Education Tools</option>
          <option value="Other">Other</option>
        </select>
      </div>
      
      <div id="hidden-other-subject" class="form-group col-xs-6 hidden">
        <label for="other-subject-field">Other</label>
        <input type="text" class="form-control" id="other-subject-field" name="other-subject-field" placeholder="Other subject" />
      </div>

      <div class="form-group col-xs-12">
        <label for="file-field">File Upload</label>
        <input type="file" id="file-field" name="bodyFile" class="form-control" required />
      </div>
      
      <div class="form-group col-xs-12">
        <label for="body-field">Message</label>
        <textarea id="body-field" name="body-field" class="form-control" placeholder="Type your message here" required></textarea>
      </div>
      
      <div class="form-group col-xs-12">
        <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>  
      </div>
      
    </form>
    <form id="form">
      <input name="file" id="uploadfile" type="file">
      <input name="filename" id="filename" type="text">
      <input id="submit" type="submit">
    </form>
  </div>