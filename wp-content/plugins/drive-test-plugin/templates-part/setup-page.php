<?php
  $root_status = 444;
  if( isset( $_POST['api-key'] ) && isset( $_POST['folder-id'] ) ) {
    if( !empty($_POST['api-key']) && !empty($_POST['folder-id']) ){
      delete_option( 'drive_bridge_api_key' );
      delete_option( 'drive_bridge_folder_id' );
      add_option( 'drive_bridge_api_key', $_POST['api-key'] ) ;
      add_option( 'drive_bridge_folder_id', $_POST['folder-id'] ) ;
      $root_status =  true;
    } else {
      $root_status =  false;
    }
  } 
  $current_api = get_option('drive_bridge_api_key')?:'';
  $current_folid = get_option('drive_bridge_folder_id')?:'';
?>
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
  <div class="wrap">
    <div class="container">
      <?php if($root_status != 444) {
        if($root_status == true ){?>
          <div class="updated error-notic  notice">
            <p><?php _e('connection establish successfully with new API KEY.', 'Drive Bridge'); ?></p>
          </div>
        <?php } else { ?>
          <div class="error error-notic  notice">
            <p><?php _e('connection establish failed with new API KEY', 'Drive Bridge'); ?></p>
          </div>
          <?php } 
      } ?>
      <div class="error error-notic drive-bridge-notic notice">
        <p><?php _e('You should really update to achieve some awesome instad of bummer', 'Drive Bridge'); ?></p>
      </div>
      <div class="row">
        <h1><?php _e('Welcome TO Drive Bridge', 'Drive Bridge'); ?></h1>
        <div>
          <?php if( empty($current_api) || empty($current_api) ): ?>
            <h3><?php _e('Please set up your Drive', 'Drive Bridge'); ?></h3>
          <?php endif; ?>
        </div>
        <form id="connect-drive" method="post" >
          <div class="drive-form">
            <label class="" for="drive-apikeys" for="title"><?php _e('Web Application URL', 'Drive Bridge'); ?></label>
            <input type="text" name="api-key" size="100%" value="<?php echo $current_api; ?>" id="drive-apikeys" spellcheck="true" autocomplete="off">
            <input type="hidden" name="folder-id" value="<?php echo $current_folid; ?>" id="folder-Id" >
          </div>
          <div class="row">
            <input type="submit" value="<?php _e('Connect To Drive', 'Drive Bridge');?> " class="button button-primary button-large" />
          </div>
        </form>
      </div>
    </div>
  </div>