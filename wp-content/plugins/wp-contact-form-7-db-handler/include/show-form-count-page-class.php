<?php
/*/ main count page show data [ys_cfdbh] /*/

class ys_cfdbh_Main_List_Table extends WP_List_Table
{
    /*/ Prepare the items for the table to process /*/
    public function prepare_items()
    {

        global $wpdb;
        $table_name  = $wpdb->prefix.'posts';
        $meta_table_name  = $wpdb->prefix.'postmeta';
        $columns     = $this->get_columns();
        $hidden      = $this->get_hidden_columns();
        $data        = $this->table_data();
        $perPage     = 10;
        $currentPage = $this->get_pagenum();
        $sortable    = $this->get_sortable_columns();
        /*/ orderBy query /*/
        $count_response  = $wpdb->get_var("SELECT COUNT(*) FROM $table_name a, $meta_table_name b WHERE a.post_type = 'wpcf7_contact_form' AND a.post_status = 'publish' AND b.post_id = a.ID AND meta_key = 'ys_cfdbh_send_in_count' ");

        $this->set_pagination_args( array(
            'total_items' => $count_response,
            'per_page'    => $perPage
        ) );

        $this->_column_headers = array($columns, $hidden, $sortable );
        $this->items = $data;
    }
    
    /*/ Override the parent columns method. Defines the columns to use in your listing table /*/
    public function get_columns()
    {


        $columns = array(
            'name' => __( 'Name', 'contact-form-ys_cfdbh' ),
            'send_in'=> __( 'Total count', 'contact-form-ys_cfdbh' ),
            'unread'=> __( 'Unread', 'contact-form-ys_cfdbh' ),
            'read'=> __( 'Read', 'contact-form-ys_cfdbh' ),
            'post_date'=> __( 'Creation Date', 'contact-form-ys_cfdbh' )
        );

        return $columns;
    }

    /*/ orderBy query /*/
    public function get_hidden_columns()
    {
        return array();
    }

    /*/ make columns sortable /*/
    public function get_sortable_columns()
    {
       return array('post_date' => array('post_date', false),
                    'send_in' => array('send_in', false));
    }

    /*/ Message to be displayed when there are no items /*/
    public function no_items() {
        _e( 'No form submission done yet.' );
    }
     /*/ Tabnle data /*/
    private function table_data()
    {
        global $wpdb;

        $data         = array();
        $table_name   = $wpdb->prefix.'posts';
        $page         = $this->get_pagenum();
        $page         = $page - 1;
        $start        = $page * 10;

        $orderdata    = ( isset( $_GET['order'] ) && !empty( $_GET['order'] ) ) ? sanitize_text_field( $_GET['order'] ) : 'ASC';
        $orderByData  = ( isset( $_GET['orderby'] ) && sanitize_text_field( $_GET['orderby'] )  === 'send_in' ) ? 'ys_cfdbh_send_in_count' : 'post_date'; 
        $args = array(
            'post_type'=> 'wpcf7_contact_form',
            'order'    => $orderdata,
            'posts_per_page' => 10,
            'meta_key' => 'ys_cfdbh_send_in_count',
            'orderby' => $orderByData,
            'offset' => $start
        );

        $main_query = new WP_Query( $args );
        if( $main_query->have_posts() ): // if recods found
            while ( $main_query->have_posts() ) : $main_query->the_post();
                $form_post_id = get_the_id();
                $count_key = 'ys_cfdbh_send_in_count';
                $count = get_post_meta($form_post_id, $count_key, true);
                /*/ if didnt get count response /*/
                if($count == '') {
                    $count = '-';
                    $read_response[0] = (object)array();
                    /*/ get readf and unread massages response/*/
                    $read_response[0]->unread = '-';
                    $read_response[0]->read = '-';
                } else {
                    $read_response = $wpdb->get_results("SELECT COUNT( IF( post_status = 'unread',1, NULL ) ) 'unread' , COUNT( IF( post_status = 'read',1, NULL ) ) 'read' FROM $table_name WHERE post_parent = $form_post_id AND post_type = 'ys_cfdbh' ");
                }
                $form_title = get_the_title() ;
                $form_date = get_the_date('Y-m-d');
                $link  = "<a class='row-title' href=admin.php?page=contact-form-dbh&post_parent=$form_post_id&order=desc&orderby=post_date>%s</a>";
                $data_value['name']  = sprintf( $link, $form_title );
                $data_value['send_in'] = sprintf( $link, $count );
                $data_value['unread'] = sprintf( $link, $read_response[0]->unread );
                $data_value['read'] = sprintf( $link, $read_response[0]->read );
                $data_value['post_date'] = sprintf( $link, $form_date );
                $data[] = $data_value;
            endwhile;
            wp_reset_postdata();
        endif;
        return $data;
    }
    
    /*/ return defult columns /*/
    public function column_default( $item, $column_name )
    {
        return $item[ $column_name ];
    }

}