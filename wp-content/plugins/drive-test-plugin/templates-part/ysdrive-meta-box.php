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

    table#showfiletable {
      width:100%;
    }
    table#showfiletable, #showfiletable th, #showfiletable td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    #showfiletable td {
      padding: 15px !important ;
      text-align: left !important; 
    }
    #showfiletable th {
      padding: 15px !important ;
      text-align: center !important; 
    }
    #showfiletable tr:nth-child(even) {
      background-color: #e3e3e3;
    }
    #showfiletable tr:nth-child(odd) {
      background-color: #fff;
    }
    #showfiletable th {
      background-color: #f3f5f6;
      color: #0071a1;
    }
  </style>
  <nav class="nav-tab-wrapper">
      <a href="" class="nav-tab nav-tab-active" ysdrive_currtab="upload-documents" ><?php _e("Upload Document","yspl-cpt-wishlist"); ?></a>
      <a href="" class="nav-tab" ysdrive_currtab="manage-checklist"> <?php _e("Manage Checklist","yspl-cpt-wishlist"); ?> </a>
  </nav>
  <div id="upload-documents" ysdrive_currtab="upload-documents" class="tab-content">
    <h3>Upload File</h3>
    <div id="drive-meta-box">
      <div class="drive-form">
          <div class="label"><label for="drive-file-name">Set File Name: </label></div>
          <div><input class="drive-input" name="drive-file-name" id="drive-file-name" type="text" /></div>
      </div>

      <div class="drive-form">
          <div class="label"><label for="file-sec-drive">Upload Image</label></div>
          <div><input id="file-sec-drive" file-data="" type="button" value="Select File To Uopload" class="button drive_upload_image_button" /><a target="_blank" href=""></a></div>
      </div>
      <div class="drive-form">
          <div><input class="drive-input-btn button button-primary button-large" value="Upload File" id="upload-file-to-drive"  type="button" /></div>
      </div>
      <div class=" head-line">
        <table id="showfiletable" style="width:100%">
          <thead>  
            <tr>
              <th><?php _e('Name', 'Drive Bridge'); ?></th>
              <th><?php _e('Size', 'Drive Bridge'); ?></th>
              <th><?php _e('FileURL', 'Drive Bridge'); ?></th>
            </tr>
          </thead>
          <tbody> 
          <tr>
            <td colspan="4"><?php _e('Please Wait...', 'Drive Bridge'); ?></td>
          </tr>             
          </tbody>
        </table>
      </div>
      <div class="updated drive-bridge-notic notice">
          <p><?php _e('Something has been updated, awesome', 'Drive Bridge'); ?></p>
      </div>
      <div class="error drive-bridge-notic notice">
          <p><?php _e('There has been an error.', 'Drive Bridge'); ?></p>
      </div>
      <div class="update-nag drive-bridge-notic notice">
          <p><?php _e('You should really update to achieve some awesome instad of bummer', 'Drive Bridge'); ?></p>
      </div>  
    </div>
  </div>
  <div id="manage-checklist" ysdrive_currtab="manage-checklist" class="tab-content" >
    <h3><?php _e('Upload Checklist', 'Drive Bridge'); ?></h3>
    <div>
      <?php $checklist = get_option( 'ysdrive_check_list' );
      if ( $checklist !== false ) { 
        if( $checklistID =  get_post_meta( get_the_ID(), 'drive_bridge_checklist_id', true ) ):  $count = 0; ?>
          <input type="hidden" name="postChecklistID" value="<?php echo $checklistID; ?>" />
          <button class="drive-input-btn upload-checklist-to-drive button button-primary button-large"><?php _e('Upload Checklist', 'Drive Bridge'); ?></button>
          <ul>
            <?php foreach ($checklist as $item) { $count++; ?>
              <li class="">
                <input type="checkbox" id="list-item-<?php echo $count;?>" name="item-<?php echo $count;?>" value="<?php echo sanitize_title($item);?>" >
                <label for="list-item-<?php echo $count;?>"><?php echo $item;?></label>
              </li>	
            <?php } ?>
          </ul>
          <button class="drive-input-btn upload-checklist-to-drive button button-primary button-large"><?php _e('Upload Checklist', 'Drive Bridge'); ?></button>
        <?php else: ?>
          <div>
              <div class="drive-form">
                <div>
                  <input class="drive-input button" value="Create Check List" id="create-check-in-drive"  type="button" />
                  <?php  echo  '<span class="ysdrive-spinner" style="display:none;"><img src="'.get_home_url().'/wp-admin/images/spinner.gif" /></span>';?>
                </div>
              </div>
          </div>
        <?php endif; ?>
      <?php  } else { ?>
          <P> <?php _e('Please add check list option for use this Drive Bridge fetured.', 'Drive Bridge'); ?> </P>
      <?php } ?>
    </div>
  </div>
 <?php //end ?>