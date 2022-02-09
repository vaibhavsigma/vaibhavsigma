<?php
/*/ The Doweload File CLass /*/

class expoert_ys_cfdbh_CSV{

    /*/ Download csv file /*/
    public function download_send_headers( $filename ) {
        // disable caching
        $current_date = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$current_date} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");

    }
    /*/ Convert array to csv format /*/
    public function array2csv(array &$array, $df){

        if (count($array) == 0) {
            return null;
        }

        $array_keys = array_keys($array);
        $heading    = array();
        $unwanted   = array('ys_cfdbh_file', 'your-'); /*/ remove unwanted names and data in columns  /*/
        $form_post_id =  ( isset( $_GET['post_parent'] ) ) ? (int) sanitize_text_field( $_GET['post_parent'] ) : 0 ;
        /*/ get New Field Names  /*/
        if( $new_columns_names = get_post_meta( $form_post_id, 'ys_cfdbh_columns_names' ) ){
            if( !is_array($new_columns_names) || !$new_columns_names = $new_columns_names[0] ) 
                $new_columns_names = array( );
        }

        if( $hide_columns = get_post_meta( $form_post_id, 'show_form_columns' ) ){
            if( !$hide_columns = $hide_columns )
                $hide_columns = array( );
        }
        foreach ( $array_keys as $aKeys ) {
            $tmp = str_replace( $unwanted, '', $aKeys );
            if( in_array($tmp, $hide_columns) ) continue;
            if( array_key_exists($tmp, $new_columns_names ) ){
                $tmp = $new_columns_names[$tmp];
            }
            $heading[] = ucfirst( $tmp );            
        }
        fputcsv( $df, $heading );
        foreach ( $array['form_id'] as $line => $form_id ) { 
            $line_values = array();
            foreach($array_keys as $array_key ) {
                if( in_array($array_key, $hide_columns) ) continue;
                $val = isset( $array[ $array_key ][ $line ] ) ? $array[ $array_key ][ $line ] : '';
                $line_values[ $array_key ] = $val;
            }
            fputcsv($df, $line_values);
        }
    }
    /*/ get Download file data from wp_posts tabel /*/
    public function download_ys_cfdbh_csv_file(){

        global $wpdb;
        $table_name  = $wpdb->prefix.'posts';
        $form_post_id =  ( isset( $_GET['post_parent'] ) ) ? (int) sanitize_text_field( $_GET['post_parent'] ) : 0 ;
        if( isset($_REQUEST['ys_cfdbh_csv'])  &&  sanitize_text_field( $_REQUEST['ys_cfdbh_csv'] ) == true  && isset( $_REQUEST['ys_cfdbh_nonce'] ) ){

            $nonce =  esc_sql( $_REQUEST['ys_cfdbh_nonce'] );
            if ( ! wp_verify_nonce( $nonce, 'ys_cfdbh_nonce') || $form_post_id == 0 ) {
                wp_die( 'Not Valid Download nonce..!! ' );
            }
            $dateConFrom = ( isset( $_GET['ys_cfdbh_start_date'] ) && !empty($_GET['ys_cfdbh_start_date']) ) ? ' AND CAST(post_modified AS DATE) >= "'.sanitize_text_field( $_GET["ys_cfdbh_start_date"] ).'" ' : '' ;
            $dateConTo = ( isset( $_GET['ys_cfdbh_end_date'] ) && !empty($_GET['ys_cfdbh_end_date']) ) ? ' AND CAST(post_modified AS DATE) <= "'.sanitize_text_field( $_GET["ys_cfdbh_end_date"] ).'" ' : '' ;
            $search_query = ( isset( $_GET['s'] ) && !empty($_GET['s']) )  ? ' AND post_content LIKE "%'.sanitize_text_field( $_GET["s"] ).'%" ' : '' ;

            $heading_row = $wpdb->get_results("SELECT ID, post_content, post_date, post_status FROM $table_name WHERE post_parent = '$form_post_id' AND post_type = 'ys_cfdbh' $dateConFrom $dateConTo $search_query ORDER BY ID DESC LIMIT 1",OBJECT);

            $heading_row    = reset( $heading_row );
            $heading_row->post_content = preg_replace_callback('!s:\d+:"(.*?)";!s', function($m) { return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; }, $heading_row->post_content);
            $heading_row    = unserialize( $heading_row->post_content );
            $heading_key    = array_keys( $heading_row );
            $rm_underscore  = apply_filters('ys_cfdbh_remove_underscore_data', true); 

            $total_rows  = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE post_parent = '$form_post_id' AND post_type = 'ys_cfdbh' $dateConFrom $dateConTo $search_query "); 
            $per_query    = 1000;
            $total_query  = ( $total_rows / $per_query );

            $this->download_send_headers( "ys_cfdbh-" . date("Y-m-d") . ".csv" );
            $df = fopen("php://output", 'w');
            $unwanted   = array('ys_cfdbh_file', 'your-');
            ob_start();

            for( $p = 0; $p <= $total_query; $p++ ){

                $offset  = $p * $per_query;
                $results = $wpdb->get_results("SELECT ID, post_content, post_date, post_status FROM $table_name WHERE post_parent = '$form_post_id' AND post_type = 'ys_cfdbh' $dateConFrom $dateConTo $search_query  ORDER BY id DESC  LIMIT $offset, $per_query",OBJECT);
                
                $data  = array();
                $count_i     = 0;
                $conColumns = array();
                 /*/ get show Field Names  /*/
                if( $hidecolumns = get_post_meta( $form_post_id, 'show_form_columns' ) ){
                    if( is_array( $hidecolumns[0] ) )
                       foreach ($hidecolumns[0] as $key => $value) {
                         $conColumns[] =  $value;
                       }        
                } 

                foreach ($results as $result) :
                    $count_i++;
                    $data['No'][$count_i]         = $count_i;
                    $data['form_id'][$count_i]    = $result->ID;
                    $data['status'][$count_i]     = $result->post_status;
                    $data['post_modified'][$count_i]  = $result->post_date;
                    $result->post_content = preg_replace_callback('!s:\d+:"(.*?)";!s', function($m) { return "s:" . strlen($m[1]) . ':"'.$m[1].'";'; }, $result->post_content);
                    $resultTmp              = unserialize( $result->post_content );
                    $upload_dir             = wp_upload_dir();
                    $ys_cfdbh_dir_url          = $upload_dir['baseurl'].'/ys_cfdbh_uploads';

                    foreach ($resultTmp as $key => $value):
                        $matches = array();
                        if(in_array($key, $conColumns)){ continue ;}
                        if( $rm_underscore ) preg_match('/^_.*$/m', $key, $matches);
                        if( ! empty($matches[0]) ) continue;

                        if (strpos($key, 'ys_cfdbh_file') !== false ){
                            $key = str_replace( $unwanted, '', $key );
                            $data[$key][$count_i] = $ys_cfdbh_dir_url.'/'.$value;
                            continue;
                        }
                        if ( is_array($value) ){
                            $data[$key][$count_i] = implode(', ', $value);
                            continue;
                        }

                        $data[$key][$count_i] = str_replace( array('&quot;','&#039;','&#047;','&#092;')
                        , array('"',"'",'/','\\'), $value );

                    endforeach;

                endforeach;
                echo $this->array2csv( $data, $df );

            }
            echo ob_get_clean();
            fclose( $df );
            die();
        }
    }
}