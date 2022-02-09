<?php
/*/ form inner page [ys_cfdbh]/*/

class ys_cfdbh_Sub_Page
{
    private $form_post_id;
    private $search;

    /*/ Constructor start subpage /*/
    public function __construct()
    {
        $this->form_post_id =  ( isset( $_GET['post_parent'] ) ) ? (int) sanitize_text_field( $_GET['post_parent'] ) : 0 ;
        $this->list_table_page();

    }
    /*/ Display the list table page /*/
    public function list_table_page()
    {
        $ListTable = new ys_cfdbh_List_Table();
        $ListTable->prepare_items(); ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2><?php echo get_the_title( $this->form_post_id ); ?></h2>
            <h4><a href='admin.php?page=contact-form-dbh' class="ys-cfdb7-a"><i class="fa fa-arrow-circle-left"></i><?php _e( ' Go Back', 'contact-form-ys-cfdb7' ); ?> </a></h4>
            <form method="post" action="">
                <?php $ListTable->search_box('Search', 'search'); ?>
                <?php $ListTable->display(); ?>
            </form>
            <input type="hidden" id="hide-column" name="hide-column" value="">
            <div id="ys-column-Modal" class="ys-modal">
              <!-- Modal content -->
              <div class="modal-content">
                <span class="close">&times;</span>
                <div class="modal-data">
                    <?php $columnList = $ListTable->get_columns();
                    if( count($columnList) > 2 ) :
                        if( $columns_names = get_post_meta( $this->form_post_id, 'ys_cfdbh_columns_names' ) ) {
                            $columns_names = $columns_names[0];
                        } else {
                            $columns_names = array();
                        }
                        $checkcon = false ;
                        if( $columns = get_post_meta( $this->form_post_id, 'show_form_columns' ) ){
                            if( is_array($columns[0]) ){
                                $columns = $columns[0];
                                $checkcon = true;
                            }
                        } ?>
                        <h3><?php _e( 'Columns List', 'contact-form-ys-cfdb7' ); ?></h3>
                        <div class="model-open-content">
                            <div class="loader">
                                <img src="<?php echo plugin_dir_url( __DIR__ ); ?>images/loader.svg" alt="loader-img">
                            </div>
                            <form id="chnage-columns-names">
                                <table>
                                    <tr> <th> <?php _e( 'Columns Name', 'contact-form-ys-cfdb7' ); ?> </th> <th> <?php _e( 'New Name', 'contact-form-ys-cfdb7' ); ?> </th> </tr>
                                    <?php foreach ($columnList as $key => $value) : 
                                        if($key == 'cb' ) continue;
                                        if ( !isset($columns_names[$key]) || empty($columns_names[$key]) ){
                                            $columns_names[$key] = $value;
                                        }
                                        if( $checkcon == true && is_array($columns) && in_array($key, $columns ) ) : ?>
                                            <tr>
                                                <td> <a href="" class="ys_cfdbh-manage-column ys-cfdb7-a disabled-color" dataclass="<?php echo $key;?>"><i class="fa fa-eye-slash"></i> <?php echo $value;?></a></td>
                                                <td> <input type="text" class="columns-names-chmages" name="<?php echo $key;?>" value="<?php echo $columns_names[$key];?>" /></td> 
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td> <a href="" class="ys_cfdbh-manage-column ys-cfdb7-a" dataclass="<?php echo $key;?>"><i class="fa fa-eye"></i> <?php echo $value;?></a> </td>
                                                <td><input type="text" class="columns-names-chmages" name="<?php echo $key;?>" value="<?php echo $columns_names[$key];?>" /></td> 
                                            </tr>
                                        <?php endif;
                                    endforeach;  ?>
                                </table>                                
                            </form>
                            <div class="form-action-btns">
                                <button id="apply-show-column" class="button button-primary button-large "> <?php _e( 'Apply and show', 'contact-form-ys-cfdb7' ); ?> </button>
                                <button id="update-show-column" class="button button-primary button-large "> <?php _e( 'Save Changes', 'contact-form-ys-cfdb7' ); ?> </button>  
                            </div>    
                            <form id="update-column-show-form">
                                <input type="hidden" name="action" value="ys_cfdbh_update_view_list" />
                                <input type="hidden" name="url" value="<?php echo admin_url( 'admin-ajax.php' ); ?>" />
                                <input type="hidden" name="Datalist" value="" />
                                <input type="hidden" name="conatctformId" value="<?php echo $this->form_post_id;?>" />
                                <input type="hidden" name="newcolumnsnames" value="" />
                            </form>   
                        </div>           
                    <?php else:
                        echo "<h3>Need at least 3 column to perfom this task.</h3>";
                    endif; ?>
                </div>
              </div>                ​
            </div>
        </div>
        <?php
        $closing_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        if( isset( $_POST['ys_cfdbh_start_date'] )  ){
            $closing_url = remove_query_arg( array( 'ys_cfdbh_start_date' ), $closing_url );
            if( !empty( $_POST['ys_cfdbh_start_date'] ) ){ 
                $closing_url .= '&ys_cfdbh_start_date='.sanitize_text_field( $_POST['ys_cfdbh_start_date'] );
            } 
        } 

        if( isset( $_POST['ys_cfdbh_end_date'] ) ){
            $closing_url = remove_query_arg( array( 'ys_cfdbh_end_date' ), $closing_url );
            if( !empty( $_POST['ys_cfdbh_end_date'] ) ){ 
                $closing_url .= '&ys_cfdbh_end_date='.sanitize_text_field( $_POST['ys_cfdbh_end_date'] );
            } 
        } 

        if( isset( $_POST['s'] ) ){
            $closing_url = remove_query_arg( array( 's' ), $closing_url );
            if( !empty( $_POST['s'] ) ){ 
                $closing_url .= '&s='.sanitize_text_field( $_POST['s'] );
            } 
        } ?> 
        <script type="text/javascript">
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, '<?php echo $closing_url;?>' );
            }
        </script>
        <?php
    }

}

/*/ WP_List_Table  included/*/ 
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/*/ Create a new table class that will extend the WP_List_Table /*/
class ys_cfdbh_List_Table extends WP_List_Table
{
    private $form_post_id;
    private $column_titles;

    public function __construct() {

        parent::__construct(
            array(
                'singular' => 'contact_form',
                'plural'   => 'contact_forms',
                'ajax'     => false
            )
        );

    }

    /*/ Prepare the items for the table to process /*/
    public function prepare_items()
    {

        $this->form_post_id =  ( isset( $_GET['post_parent'] ) ) ? (int) sanitize_text_field( $_GET['post_parent'] ) : 0 ;
        $search       =  false ;
        if( isset( $_POST['s'] ) ) {
            if( !empty( $_POST['s']) )
                $search = sanitize_text_field( $_POST['s'] );
        } else {
            if( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ){
                 $search = sanitize_text_field( $_GET["s"] );
            }
        }
        echo $this->search;
        $form_post_id  = $this->form_post_id;

        global $wpdb;

        $this->process_bulk_action();

        $table_name  = $wpdb->prefix.'posts';
        $columns     = $this->get_columns();
        $hidden      = $this->get_hidden_columns();
        $sortable    = $this->get_sortable_columns();
        $data        = $this->table_data();

        //usort( $data, array( &$this, 'sort_data' ) );

        $perPage     = 10;
        $currentPage = $this->get_pagenum();

        $dateConFrom = $dateConTo = '' ;
        if( isset( $_POST['ys_cfdbh_start_date'] ) ) {
            $dateConFrom = empty($_POST['ys_cfdbh_start_date'])  ? '' : ' AND CAST(post_modified AS DATE) >= "'.sanitize_text_field( $_POST["ys_cfdbh_start_date"] ).'" ';
        } else {
            if( isset( $_GET['ys_cfdbh_start_date'] ) && !empty( $_GET['ys_cfdbh_start_date'] ) ){
                 $dateConFrom = ' AND CAST(post_modified AS DATE) >= "'.sanitize_text_field( $_GET["ys_cfdbh_start_date"] ).'" ';
            }
        }

        if( isset( $_POST['ys_cfdbh_end_date'] ) ) {
            $dateConTo = empty($_POST['ys_cfdbh_end_date']) ? '' : ' AND CAST(post_modified AS DATE) <= "'. sanitize_text_field( $_POST["ys_cfdbh_end_date"] ).'" ';
        } else {
            if( isset( $_GET['ys_cfdbh_end_date'] ) && !empty( $_GET['ys_cfdbh_end_date'] ) ){
                $dateConTo = ' AND CAST(post_modified AS DATE) <= "'.sanitize_text_field( $_GET["ys_cfdbh_end_date"] ).'" ';
            }
        }

        if ( ! empty($search) ) {

            $totalItems  = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE post_content LIKE '%$search%' AND post_type = 'ys_cfdbh' AND post_parent = '$form_post_id' $dateConFrom $dateConTo ");
         }else{
            $totalItems  = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE post_parent = '$form_post_id' AND post_type = 'ys_cfdbh' $dateConFrom $dateConTo ");
        }

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $this->_column_headers = array($columns, $hidden ,$sortable);
        $this->items = $data;
    }

    /*/ Override the parent columns method. Defines the columns to use in your listing table /*/
    public function get_columns()
    {
        $form_post_id  = $this->form_post_id;

        global $wpdb;
        $table_name    = $wpdb->prefix.'posts';
        $results       = $wpdb->get_results( "
            SELECT * FROM $table_name WHERE post_parent = $form_post_id AND post_type = 'ys_cfdbh' ORDER BY ID DESC LIMIT 1", OBJECT 
        );
        if($results):
            $results[0]->post_content = preg_replace_callback('!s:\d+:"(.*?)";!s', function($m) { return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; }, $results[0]->post_content);
        else:
            $results =  false;
        endif;
        // var_dump(unserialize($data));
        // print_r($results[0]->post_content);
        $first_row            = isset($results[0]) ? unserialize( $results[0]->post_content ) : 0 ;
        $columns              = array();
        $rm_underscore        = apply_filters('remove_underscore_data', true); 

        if( !empty($first_row) ){
            $columns['cb']      = '<input type="checkbox" />';
            foreach ($first_row as $key => $value) {
                $matches = array();

                if( $rm_underscore ) preg_match('/^_.*$/m', $key, $matches);
                if( ! empty($matches[0]) ) continue;

                $key_val       = str_replace( array('your-', 'ys_cfdbh_file'), '', $key);
                $columns[$key_val] = ucfirst( $key_val );
                
                $this->column_titles[] = $key_val;

            }
            $columns['post_modified'] = 'Date';
            $columns['status'] = 'Status';
        } else{
        }


        return $columns;
    }
    /*/ Define check box for bulk action (each row) /*/
    public function column_cb($item){
        return sprintf(
             '<input type="checkbox" name="%1$s[]" value="%2$s" />',
             $this->_args['singular'],
             $item['ID']
        );
    }
    /*/ Define which columns are hidden /*/
    public function get_hidden_columns()
    {
        return  array('ID');
    }
    /*/ Define the sortable columns /*/
    public function get_sortable_columns()
    {
       return array('post_modified' => array('post_modified', true));
    }

    /*/ Print column headers, accounting for hidden and sortable columns /*/
    public function print_column_headers( $with_id = true ) {
        list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        $current_url = remove_query_arg( 'paged', $current_url );

        if ( isset( $_GET['orderby'] ) ) {
            $current_orderby = sanitize_text_field( $_GET['orderby'] );
        } else {
            $current_orderby = '';
        }

        if ( isset( $_GET['order'] ) && 'desc' === sanitize_text_field( $_GET['order'] ) ) {
            $current_order = 'desc';
        } else {
            $current_order = 'asc';
        }

        if( isset( $_POST['ys_cfdbh_start_date'] ) ) {
            $current_url = remove_query_arg( 'ys_cfdbh_start_date', $current_url ); 
            if( !empty( $_POST['ys_cfdbh_start_date'] ) ){
                $current_url .= "&ys_cfdbh_start_date=".sanitize_text_field( $_POST['ys_cfdbh_start_date'] ).'';
            }
        }

        if( isset( $_POST['ys_cfdbh_end_date'] ) ) {
            $current_url = remove_query_arg( 'ys_cfdbh_end_date', $current_url ); 
            if( !empty( $_POST['ys_cfdbh_end_date'] ) ){
                $current_url .= "&ys_cfdbh_end_date=".sanitize_text_field( $_POST['ys_cfdbh_end_date'] ).'';
            }
        }

        if ( ! empty( $columns['cb'] ) ) {
            static $cb_counter = 1;
            $columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
                . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
            $cb_counter++;
        }

        foreach ( $columns as $column_key => $column_display_name ) {
            $class = array( 'manage-column', "column-$column_key" );

            if ( in_array( $column_key, $hidden ) ) {
                $class[] = 'hidden';
            }

            if ( 'cb' === $column_key ) {
                $class[] = 'check-column';
            } elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) ) {
                $class[] = 'num';
            }

            if ( $column_key === $primary ) {
                $class[] = 'column-primary';
            }

            if ( isset( $sortable[ $column_key ] ) ) {
                list( $orderby, $desc_first ) = $sortable[ $column_key ];

                if ( $current_orderby === $orderby ) {
                    $order   = 'asc' === $current_order ? 'desc' : 'asc';
                    $class[] = 'sorted';
                    $class[] = $current_order;
                } else {
                    $order   = $desc_first ? 'desc' : 'asc';
                    $class[] = 'sortable';
                    $class[] = $desc_first ? 'asc' : 'desc';
                }

                $column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
            }

            $tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
            $scope = ( 'th' === $tag ) ? 'scope="col"' : '';
            $id    = $with_id ? "id='$column_key'" : '';

            if ( ! empty( $class ) ) {
                $class = "class='" . join( ' ', $class ) . "'";
            }

            echo "<$tag $scope $id $class>$column_display_name</$tag>";
        }
    }
    /*/ Define bulk action /*/
    public function get_bulk_actions() {

        return array(
            'read'   => __( 'Read', 'contact-form-ys_cfdbh' ),
            'unread' => __( 'Unread', 'contact-form-ys_cfdbh' ),
            'delete' => __( 'Delete', 'contact-form-ys_cfdbh' )
        );
    }

    /*/ Message to be displayed when there are no items /*/
    public function no_items() {
        if( isset($_GET['ys_cfdbh_start_date']) || isset($_GET['ys_cfdbh_end_date']) || isset($_GET['s']) ){
            _e( 'No items found. Click Here to <b><a href="admin.php?page=contact-form-dbh&post_parent='.$this->form_post_id.'&order=desc&orderby=post_modified" >Revert Changes</a></b> On Page.' );
        } else {
            _e( 'No items found.' );
        }
    }

    /*/ Get the table data /*/
    private function table_data()
    {
        $data = array();
        global $wpdb;
        $search       =  false ;
        if( isset( $_POST['s'] ) ) {
            if( !empty( $_POST['s']) )
                $search = sanitize_text_field( $_POST['s'] );
        } else {
            if( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ){
                 $search = sanitize_text_field( $_GET["s"] );
            }
        }
        $table_name   = $wpdb->prefix.'posts';
        $page         = $this->get_pagenum();
        $page         = $page - 1;
        $start        = $page * 10;
        $form_post_id = $this->form_post_id;

        $orderby = isset($_GET['orderby']) ? 'post_modified' : 'ID';
        $order   = isset($_GET['order']) ? sanitize_text_field( $_GET['order'] ) : 'desc';
        $order   = esc_sql($order);
        $dateConFrom = $dateConTo = '' ;
        if( isset( $_POST['ys_cfdbh_start_date'] ) ) {
            $dateConFrom = empty($_POST['ys_cfdbh_start_date'])  ? '' : ' AND CAST(post_modified AS DATE) >= "'.sanitize_text_field( $_POST["ys_cfdbh_start_date"] ).'" ';
        } else {
            if( isset( $_GET['ys_cfdbh_start_date'] ) && !empty( $_GET['ys_cfdbh_start_date'] ) ){
                 $dateConFrom = ' AND CAST( post_modified AS DATE ) >= "'.sanitize_text_field( $_GET["ys_cfdbh_start_date"] ).'" ';
            }
        }

        if( isset( $_POST['ys_cfdbh_end_date'] ) ) {
            $dateConTo = empty($_POST['ys_cfdbh_end_date']) ? '' : ' AND CAST(post_modified AS DATE) <= "'.sanitize_text_field( $_POST["ys_cfdbh_end_date"] ).'" ';
        } else {
            if( isset( $_GET['ys_cfdbh_end_date'] ) && !empty( $_GET['ys_cfdbh_end_date'] ) ) {
                $dateConTo = ' AND CAST( post_modified AS DATE ) <= "'.sanitize_text_field( $_GET["ys_cfdbh_end_date"] ).'" ';
            }
        }
        if ( ! empty($search) ) {
           $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE  post_content LIKE '%$search%' AND post_parent = '$form_post_id' AND post_type = 'ys_cfdbh' $dateConFrom $dateConTo ORDER BY $orderby $order LIMIT $start,10", OBJECT );
        } else {
            $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE post_parent = $form_post_id AND post_type = 'ys_cfdbh' $dateConFrom $dateConTo    ORDER BY $orderby $order LIMIT $start,10", OBJECT );
        }

        foreach ( $results as $result ) {
            $key_val = str_replace( array('your-', 'ys_cfdbh_file'), '', $result->post_content);
            $result->post_content = preg_replace_callback('!s:\d+:"(.*?)";!s', function($m) { return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; }, $result->post_content);
            $form_value = unserialize( $result->post_content );

            $link  = "<b><a href=admin.php?page=contact-form-dbh&ys_cfdbh_post_id=%s>%s</a></b>";
            if(isset($result->post_status) && ( $result->post_status === 'read' ) ) { 
                $link  = "<a class='ys-inner-link' href=admin.php?page=contact-form-dbh&ys_cfdbh_post_id=%s>%s</a>";
            }

            $fid = $result->ID;
            $new_form_value = array( '' );
            foreach ($form_value as $key => $value) {
                $key_val = str_replace( array('your-', 'ys_cfdbh_file'), '', $key);
                $new_form_value[$key_val] = $value;
            }
            $form_value = $new_form_value;
            $form_values['post_parent'] = $result->post_parent;
            $form_values['ID'] = $result->ID;
            foreach ( $this->column_titles as $col_title) {
                $form_value[ $col_title ] = isset( $form_value[ $col_title ] ) ? $form_value[ $col_title ] : '';
            }
            /*Set Empty feild value*/
             $checkColumns = $this->get_columns();
            foreach ($checkColumns as $key => $value) {

                if( !array_key_exists($key,$form_value) || empty($form_value[$key]) || $form_value[$key] == " "){
                    $form_value[$key] = "";
                }
            }

            foreach ($form_value as $k => $value) {

                $tempKey = $k;

                $can_foreach = is_array($value) || is_object($value);

                if ( $can_foreach ) {

                    foreach ($value as $k_val => $val):
                        $val                = esc_html( $val );
                        $form_values[$tempKey] = ( strlen($val) > 70 ) ? substr($val, 0, 70).'...': $val;
                        $form_values[$tempKey] = sprintf($link, $result->ID, $form_values[$tempKey]);
                    endforeach;
                }else{
                    $value = esc_html( $value );
                    $form_values[$tempKey] = ( strlen($value) > 70 ) ? substr($value, 0, 70).'...': $value;
                    $form_values[$tempKey] = sprintf($link, $result->ID, $form_values[$tempKey]);
                }

            }
            $form_values['post_modified'] = sprintf($link, $result->ID, $result->post_modified );
            if(isset($result->post_status) && ( $result->post_status === 'read' ) ) {
                $form_values['status'] = '<i class="fa fa-check-circle fa-2x" style="color:#1b9800;"></i>';
            } else {
                $form_values['status'] = '<i class="fa fa-check-circle fa-2x" style="color:#aa3700;"></i>';
            }
            $data[] = $form_values;
        }
        return $data;
    }

    /*/ Define bulk action /*/
    public function process_bulk_action(){

        global $wpdb;
        $table_name = $wpdb->prefix.'posts';
        $action     = $this->current_action();

        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
            $nonce        = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $nonce_action = 'bulk-' . $this->_args['plural'];

            if ( !wp_verify_nonce( $nonce, $nonce_action ) ){
                wp_die( 'Not valid..!!' );
            }
        }

        if( 'delete' === $action ) {
            $postIds = esc_sql( $_POST['contact_form'] );
            $delete_count = 0;
            foreach ($postIds as $ID):

                $results       = $wpdb->get_results( "SELECT * FROM $table_name WHERE ID = $ID AND post_type = 'ys_cfdbh' LIMIT 1", OBJECT );
                $result_value  = $results[0]->post_content;
                $result_values = unserialize($result_value);
                $upload_dir    = wp_upload_dir();
                $ID   =  $results[0]->ID;
                $ys_cfdbh_dirname = $upload_dir['basedir'].'/ys_cfdbh_uploads';

                foreach ($result_values as $key => $result) {

                   if ( ( strpos($key, 'ys_cfdbh_file') !== false ) && file_exists($ys_cfdbh_dirname.'/'.$result) ) {
                        unlink($ys_cfdbh_dirname.'/'.$result);
                   }
                }

                $wpdb->delete(
                    $table_name ,
                    array( 'ID' => $ID ),
                    array( '%d' )
                );
                $delete_count++;
                
            endforeach;
            /*/ updeate count value in for submitions /*/
            if( $this->form_post_id ){
                $count_key = 'ys_cfdbh_send_in_count';
                $count = get_post_meta( $this->form_post_id, $count_key, true);
                if($count==''){
                    $count = 0;
                    delete_post_meta( $this->form_post_id, $count_key);
                    add_post_meta( $this->form_post_id, $count_key, '0');
                }else{
                    $count -= $delete_count ;
                    if($count < 0 ) $count = 0;
                    update_post_meta( $this->form_post_id, $count_key, $count);
                }
            }

        }else if( 'read' === $action ){

            $postIds = esc_sql( $_POST['contact_form'] );
            foreach ($postIds as $IDs):
                $wpdb->query( "UPDATE $table_name SET post_status = 'read' WHERE ID = '$IDs' AND post_type = 'ys_cfdbh' "  );

            endforeach;

        }else if( 'unread' === $action ){
            $postIds = esc_sql( $_POST['contact_form'] );
            foreach ($postIds as $IDs):
                $wpdb->query( "UPDATE $table_name SET post_status = 'unread' WHERE ID = '$IDs' AND post_type = 'ys_cfdbh' "   );
            endforeach;
        }else{
        }
    }

    /*/ Define what data to show on each column of the table /*/
    public function column_default( $item, $column_name )
    {
        return $item[ $column_name ];

    }

    /*/ Allows you to sort the data by the variables set in the $_GET /*/
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'form_date';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = sanitize_text_field( $_GET['orderby'] );
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = sanitize_text_field( $_GET['order'] );
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return $result;
    }

    /*/Display the pagination. /*/
    protected function pagination( $which ) {
        if ( empty( $this->_pagination_args ) ) {
            return;
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        $dateConFrom = $dateConTo = $searchvalue = '';

        if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }

        if ( 'top' === $which && $total_pages > 1 ) {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }

        $output = '<span class="displaying-num">' . sprintf(
            /* translators: %s: Number of items. */
            _n( '%s item', '%s items', $total_items ),
            number_format_i18n( $total_items )
        ) . '</span>';

        $current              = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();

        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

        $current_url = remove_query_arg( $removable_query_args, $current_url );
        /*/ pagination ys_cfdbh conditions start here. /*/
        if( isset( $_POST['ys_cfdbh_start_date'] )  ){
          if(empty( $_POST['ys_cfdbh_start_date'] ) ){ 
            $current_url = remove_query_arg( array( 'ys_cfdbh_start_date' ), $current_url );
          } else {
            $dateConFrom = '&ys_cfdbh_start_date='.sanitize_text_field( $_POST['ys_cfdbh_start_date'] ).'';
          }
        } 

        if( isset( $_POST['ys_cfdbh_end_date'] ) ){
            if(empty( $_POST['ys_cfdbh_end_date'] ) ){ 
            $current_url = remove_query_arg( array( 'ys_cfdbh_end_date' ), $current_url );
          } else {
            $dateConTo = '&ys_cfdbh_end_date='.sanitize_text_field( $_POST['ys_cfdbh_end_date'] ).'';
          }
        } 

        if( isset( $_POST['s'] ) ){
            if(empty( $_POST['s'] ) ){ 
            $current_url = remove_query_arg( array( 's' ), $current_url );
          } else {
            $searchvalue = '&s='.sanitize_text_field( $_POST['s'] ).'';
          }
        } 

        $current_url .=  ''.$dateConFrom.''.$dateConTo.''.$searchvalue.'';
        /*/ pagination ys_cfdbh conditions end here. /*/

        $page_links = array();

        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';

        $disable_first = false;
        $disable_last  = false; 
        $disable_prev  = false;
        $disable_next  = false;

        if ( 1 == $current ) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ( 2 == $current ) {
            $disable_first = true;
        }
        if ( $total_pages == $current ) {
            $disable_last = true;
            $disable_next = true;
        }
        if ( $total_pages - 1 == $current ) {
            $disable_last = true;
        }

        if ( $disable_first ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='first-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( remove_query_arg( 'paged', $current_url ) ),
                __( 'First page' ),
                '&laquo;'
            );
        }

        if ( $disable_prev ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='prev-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ),
                __( 'Previous page' ),
                '&lsaquo;'
            );
        }

        if ( 'bottom' === $which ) {
            $html_current_page  = $current;
            $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf(
                "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                $current,
                strlen( $total_pages )
            );
        }
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
        $page_links[]     = $total_pages_before . sprintf(
            /* translators: 1: Current page, 2: Total pages. */
            _x( '%1$s of %2$s', 'paging' ),
            $html_current_page,
            $html_total_pages
        ) . $total_pages_after;

        if ( $disable_next ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='next-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ),
                __( 'Next page' ),
                '&rsaquo;'
            );
        }

        if ( $disable_last ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='last-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
                __( 'Last page' ),
                '&raquo;'
            );
        }

        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

        if ( $total_pages ) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        echo $this->_pagination;
    }

    /*/ Display the bulk actions dropdown. /*/
    protected function bulk_actions( $which = '' ) {
        if ( is_null( $this->_actions ) ) {
            $this->_actions = $this->get_bulk_actions();
            /*/ Filters the list table Bulk Actions drop-down. /*/
            $this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
            $two = '';
        } else {
            $two = '2';
        }

        if ( empty( $this->_actions ) )
            return;

        echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action', 'contact-form-ys_cfdbh' ) .     '</label>';
        echo '<select name="action' . $two . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
        echo '<option value="-1">' . __( 'Bulk Actions', 'contact-form-ys_cfdbh' ) . "</option>\n";

        foreach ( $this->_actions as $name => $title ) {
            $class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

            echo "\t" . '<option value="' . $name . '"' . $class . '>' . $title . "</option>\n";
        }

        echo "</select>\n";
        $start_date = $dateConFrom = $end_date = $dateConTo = '';
        if( isset( $_POST['ys_cfdbh_start_date'] ) ){
            if( !empty( $_POST['ys_cfdbh_start_date'] ) ) {    
                $start_date = sanitize_text_field( $_POST['ys_cfdbh_start_date'] ); 
                $dateConFrom = '&ys_cfdbh_start_date='.$start_date.'';
            } 
        } else {
            if( isset( $_GET['ys_cfdbh_start_date'] ) && !empty( $_GET['ys_cfdbh_start_date'] ) ){
                $start_date = sanitize_text_field( $_GET['ys_cfdbh_start_date'] ); 
                $dateConFrom = '&ys_cfdbh_start_date='.$start_date.'';
            }
        }
        if( isset( $_POST['ys_cfdbh_end_date'] ) ){
            if( !empty( $_POST['ys_cfdbh_end_date'] ) ) {
                $end_date = sanitize_text_field( $_POST['ys_cfdbh_end_date'] ); 
                $dateConTo = '&ys_cfdbh_end_date='.$end_date.'';
            } 
        } else {
            if( isset( $_GET['ys_cfdbh_end_date'] ) && !empty( $_GET['ys_cfdbh_end_date'] ) ){
                $end_date = sanitize_text_field( $_GET['ys_cfdbh_end_date'] ); 
                $dateConTo = '&ys_cfdbh_end_date='.$end_date.'';
            }
        } 

        echo ' <b>'. __( 'From', 'contact-form-ys_cfdbh' ).'</b> <input placeholder="Form"  type="date" max="'.date('Y-m-d').'" class="ys_cfdbh_start_date"  name="ys_cfdbh_start_date" value="'.$start_date.'" />';
        echo ' <b>'. __( 'To', 'contact-form-ys_cfdbh' ).'</b> <input placeholder="To" type="date" max="'.date('Y-m-d').'" min="'.$start_date.'" class="ys_cfdbh_end_date" name="ys_cfdbh_end_date" value="'.$end_date.'" /> ';
        echo " <button type='Reset' class='button ys_cfdbh-reset-button'>";
        _e( 'Reset Date', 'contact-form-ys_cfdbh' );
        echo '</button> ';
        submit_button( __( 'Apply', 'contact-form-ys_cfdbh' ), 'action', '', false, array( 'id' => "doaction$two" ) );
        echo "\n";
        echo "<button class='button ys_cfdbh-show-columns-model' style='margin:0 10px;'><i class='fa fa-eye'></i>  ";
        _e( 'View column', 'contact-form-ys_cfdbh' );
        echo '</button>';
        $nonce = wp_create_nonce( 'ys_cfdbh_nonce' );
        $current_url =  sanitize_text_field( $_SERVER['REQUEST_URI'] );
        if( isset( $_GET['ys_cfdbh_start_date'] ) || empty( $_GET['ys_cfdbh_start_date'] ) ){
            $current_url = remove_query_arg( array( 'ys_cfdbh_start_date' ), $current_url );
        }
        if( isset( $_GET['ys_cfdbh_end_date'] )  || empty( $_GET['ys_cfdbh_end_date'] )  ){
            $current_url = remove_query_arg( array( 'ys_cfdbh_end_date' ), $current_url );
        }
        if( isset( $_POST['s'] ) ){
            $current_url = remove_query_arg( array( 's' ), $current_url );
            if( !empty( $_POST['s'] ) ){ 
                $current_url .= '&s='.sanitize_text_field( $_POST['s'] );
            } 
        }
        echo "<a href='".$current_url."&ys_cfdbh_csv=true&ys_cfdbh_nonce=".$nonce."".$dateConFrom."".$dateConTo."' style='float:right; margin:0;' class='button'>";
        _e( 'Export CSV', 'contact-form-ys_cfdbh' );
        echo '</a>';       
    }
}
