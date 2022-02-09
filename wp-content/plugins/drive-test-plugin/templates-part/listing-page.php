<?php /*/-----> Uplaod form <-----/*/?>
<style type="text/css">
  .list-form{ margin-top:20px; }
  input{ width : 80%; }
  input{ height : 40px; }
  input{ margin : 0 10px; }
  input{ font-size : 24px; }
  label{ font-size : 28px; color: #2471B1; min-width: 97px; display:inline-block; text-align:center; }
  li{display:block;}
  label > span{text-align:rigth;}
  h6.reponse-msg.error{ border: 3px red solid; padding: 5px; }
  h6.reponse-msg.success{ border: 3px green solid; padding: 5px; }
</style>
<div class="container">
  <h2>Create Your Check List</h2>
  <p>Check List Points</p>
  <form class="list-form">
    <ul class="check-list">
      <?php $checklist = get_option( 'ysdrive_check_list' );
      if ( $checklist !== false ) {  
        $count = 0;
        foreach ($checklist as $item) { $count++; ?>
        <li class="listitems">
          <label for="list-item-<?php echo $count;?>">No. <?php echo sprintf("%02d", $count);?> <span>:</span></label><a class="button button-primary ysdrive-remove-list" item="item-<?php echo $count;?>">Remove</a><input type="text" id="list-item-<?php echo $count;?>" name="item-<?php echo $count;?>" value="<?php echo $item;?>" required=""></li>
        <?php }
      }?>
    </ul>
    <a class="button button-primary ysdrive-add-list" >Add More</a>
    <button type="submit" class="button button-primary ysdrive-update-list" >Update List</button>
    <input type="hidden" name="action" value="ysdriveUpdateCheckList">
    <input type="hidden" id="ajax-url" value="<?php echo admin_url('admin-ajax.php'); ?>">
  </form>
  <h6 class="reponse-msg"></h6>
</div>
<?php //end ?>