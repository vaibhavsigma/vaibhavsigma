<?php /*/-----> Uplaod form <-----/*/?>
  <style type="text/css"> 
    .drive-bridge-notic{
        display: none;
    }
    .drive-form > .label{
      font-size: 12px; 
      font-weight: 700;  
      margin: 0 0 3px;       
    }
    .drive-form > input, .drive-input{
      width: 100%;        
    }
    .head-line{
      border-top: #EEEEEE solid 1px;
    }
    .drive-form {
      padding: 15px 12px;
      margin: 0;
    }
    .drive-form a{
      padding: 15px 12px;
      margin: 0;
    }

    
  </style>
  <div id="drive-meta-box">
    <div>
        <div class="drive-form">
            <div>
              <input class="drive-input button" value="Connect Post Google Drive" id="connect-post-to-drive"  type="button" />
              <?php  echo  '<span class="ysdrive-spinner" style="display:none;"><img src="'.get_home_url().'/wp-admin/images/spinner.gif" /></span>';?>
            </div>
        </div>
    </div>
  </div>
 <?php //end ?>