<?php
 /*/  single information / details page of submited data ys_cfdbh /*/
class ys_cfdbh_Form_Details
{
    private $ys_cfdbh_post_id;
    public function __construct()
    {
       $this->ys_cfdbh_post_id = ( isset( $_GET['ys_cfdbh_post_id'] ) ) ? (int) sanitize_text_field( $_GET['ys_cfdbh_post_id'] ) : 0 ;

       $this->form_details_page();
    }

    /*/  single information / details class /*/
    public function form_details_page(){
        global $wpdb;
        $table_name    = $wpdb->prefix.'posts';
        $upload_dir    = wp_upload_dir();
        $ys_cfdbh_dir_url = $upload_dir['baseurl'].'/ys_cfdbh_uploads';
        $rm_underscore = apply_filters('ys_cfdbh_remove_underscore_data', true); 

        if ( is_numeric($this->ys_cfdbh_post_id) ) {
           $results    = $wpdb->get_results( "SELECT * FROM $table_name WHERE ID = $this->ys_cfdbh_post_id AND post_type = 'ys_cfdbh' LIMIT 1", OBJECT );
        }

        if ( empty($results) ) {     
            wp_die( $message = '<h2 style="margin: 3% 1%; font-size: 22px;" >Record Not Found.</h2>' ); 
        } 
         /*/ get New Column Names  /*/
        if( $new_columns_names = get_post_meta( $results[0]->post_parent, 'ys_cfdbh_columns_names' ) ){
            if( !is_array($new_columns_names[0]) ) {
                $new_columns_names = array( );
            } else {
                $new_columns_names = $new_columns_names[0];
            }
        } else {
            $new_columns_names = array( );
        }
        $results[0]->post_content = preg_replace_callback('!s:\d+:"(.*?)";!s', function($m) { return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; }, $results[0]->post_content);
        ?>
        <div class="wrap">
            <div id="welcome-panel" class="welcome-panel">
                <div class="welcome-panel-content">
                    <div class="welcome-panel-column-container">
                        <h4><a href='admin.php?page=contact-form-dbh&post_parent=<?php echo $results[0]->post_parent; ?>&order=desc&orderby=post_modified' class="ys-cfdb7-a"><i class="fa fa-arrow-circle-left"></i><?php _e( ' Go Back', 'contact-form-ys-cfdb7' ); ?> </a></h4>
                        <h3><?php echo get_the_title( $results[0]->post_parent ); ?></h3>
                        <p></span><?php echo $results[0]->post_modified; ?></p>
                        <?php $form_data  = unserialize( $results[0]->post_content ); ?>
                        <table class='detail-page-tabel'>
                            <?php foreach ($form_data as $key => $data):
                                $matches = array();
                                if( $rm_underscore ) preg_match('/^_.*$/m', $key, $matches);
                                if( ! empty($matches[0]) ) continue;
                                if ( strpos($key, 'ys_cfdbh_file') !== false ){
                                    $key_val = str_replace('ys_cfdbh_file', '', $key);
                                    $key_val = str_replace('your-', '', $key_val);
                                    $key_val = ucfirst( $key_val );
                                    if( array_key_exists($key, $new_columns_names ) ){
                                        $key_val = $new_columns_names[$key];
                                    } ?>
                                    <tr> 
                                        <td> <b> <?php echo $key_val; ?> </b> </td> 
                                        <td> &emsp; <a href="<?php echo $ys_cfdbh_dir_url.'/'.$data ; ?>" target="_blank"> <?php echo $data; ?> </a> </td>
                                    </tr>
                                <?php }else{
                                    if ( is_array($data) ) {

                                        $key_val = str_replace('your-', '', $key);
                                        $key_val = ucfirst( $key_val );
                                        $arr_str_data =  implode(', ',$data);
                                        $arr_str_data =  esc_html( $arr_str_data );
                                        if ( empty($arr_str_data) || $arr_str_data == '  ' ) {
                                            $arr_str_data =  ' - ' ;
                                        }
                                        if( array_key_exists($key, $new_columns_names ) ){
                                            $key_val = $new_columns_names[$key];
                                        } ?>
                                        <tr> 
                                            <td> <b><?php echo $key_val; ?></b> </td>
                                            <td> &emsp; <?php echo  $arr_str_data; ?> </td> 
                                        </tr>
                                    <?php }else{
                                        $key_val = str_replace('your-', '', $key);
                                        $key_val = ucfirst( $key_val );
                                        $data    = esc_html( $data );
                                        if ( empty($data) || $data == ' ' ) {
                                            $data =  ' ––– ' ;
                                        }
                                        if( array_key_exists($key, $new_columns_names ) ){
                                            $key_val = $new_columns_names[$key];
                                        } ?>
                                        <tr> 
                                            <td> <b><?php echo $key_val; ?></b> </td>
                                            <td> &emsp; <?php echo $data; ?> </td>
                                        </tr>
                                    <?php }
                                }
                            endforeach; ?>
                        </table>
                        <?php if( $results[0]->post_status != "read") {
                            $wpdb->query( "UPDATE $table_name SET post_status = 'read' WHERE ID = $this->ys_cfdbh_post_id  AND post_type = 'ys_cfdbh' " ); 
                        } ?>
                        <div style="margin: 20px 0;"></div>
                    </div>
                </div>
            </div>
        </div> 
    <?php  }
}