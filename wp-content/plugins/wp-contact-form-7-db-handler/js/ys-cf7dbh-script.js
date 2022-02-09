/*/---- Custom Script For Happy Professional ----/*/
jQuery(document).ready( function() {
	// When the user clicks the button, open the modal 
    jQuery('.ys_cfdbh-show-columns-model').click( function( e ) {
      e.preventDefault();
      var $column = jQuery(".manage-column");

      jQuery('#ys-column-Modal').show();
    } );

    // When the user clicks on <span> (x), close the modal
    jQuery('.close').click(function() {
      jQuery('#ys-column-Modal').hide();
    });

    // When the user clicks anywhere outside of the modal, close it
    jQuery('#ys-column-Modal').click(function(event) {
        jQuery('#ys-column-Modal').hide();
    }).children().click(function(e) {
      return false;
    });

    jQuery('.ys_cfdbh-manage-column').click(function( e ) {
        e.preventDefault();
        if( jQuery(this).find(".fa").hasClass('fa-eye-slash') ){
            jQuery(this).find(".fa").attr("class",'fa fa-eye');
            jQuery(this).removeClass('disabled-color');
        }else {
            jQuery(this).find(".fa").attr("class",'fa fa-eye-slash');
            jQuery(this).addClass('disabled-color');
        }
    });

     jQuery('#apply-show-column').click(function( e ) {
        e.preventDefault();
        jQuery('.ys_cfdbh-manage-column').map(function(){
           var classAttr = jQuery(this).attr('dataclass');
           if(jQuery(this).hasClass( "disabled-color" ) ){
               jQuery('.column-'+classAttr).hide();               
           } else {
               jQuery('.column-'+classAttr).show();
           }
        });
        chnage_columns_names();
        jQuery('#ys-column-Modal').hide();
    });

    jQuery('#update-show-column').click(function( e ) {
        e.preventDefault();
        var $currentBtn = jQuery(this);
        var $colmns = '';
        jQuery('.ys_cfdbh-manage-column').map(function(){
           var classAttr = jQuery(this).attr('dataclass');
           if( jQuery(this).hasClass( "disabled-color" ,) ){
               jQuery(this).addClass('hide-data-column');
               if($colmns != '') {
                   $colmns += ','+classAttr;
               } else {
                   $colmns = classAttr;
               }
           } 
        });
        jQuery('#update-column-show-form input[name="Datalist"]').val(''+$colmns);
        var url = jQuery('#update-column-show-form input[name="url"]').val();
        jQuery('#update-column-show-form input[name="newcolumnsnames"]').val(''+jQuery("#chnage-columns-names").serialize() );
        jQuery('.model-open-content').addClass('ys-cfdb7-active-modal');
        jQuery.ajax({
            type : "post",
            url : url,
            data : jQuery("#update-column-show-form").serialize() ,
            success: function(response) {
               if(response.search("successfuly")){
                   jQuery(".loading-btn").addClass("hide");
                   jQuery('.ys_cfdbh-manage-column').removeClass('disabled-color');
                   jQuery('.ys_cfdbh-manage-column').map( function(){
                       var classAttr = jQuery(this).attr('dataclass'); 
                       if( jQuery(this).hasClass( "hide-data-column" ) ) {
                           jQuery(this).addClass('disabled-color');
                           jQuery(this).find(".fa").attr("class",'fa fa-eye-slash');
                           jQuery(this).removeClass('hide-data-column');
                           jQuery('.column-'+classAttr).hide();
                       } else {                          
                          jQuery('.column-'+classAttr).show();
                          jQuery(this).find(".fa").attr("class",'fa fa-eye');
                       }
                       chnage_columns_names();
                    } );
                   console.log( 'sucessfully Added class ...' );
                   jQuery('.model-open-content').removeClass('ys-cfdb7-active-modal');
                   jQuery('#ys-column-Modal').hide();
                } else {
                    jQuery('.hide-data-column').removeClass('hide-data-column');
                    jQuery('.model-open-content').removeClass('ys-cfdb7-active-modal');
                    console.log( 'unable to Update List' );
                }
            },
            error: function(xhr, status, error) {
            console.log("Error :  "+error );
            jQuery(".loading-btn").addClass("hide");
          }
        });
    });
    
    jQuery('.ys_cfdbh_start_date , .ys_cfdbh_end_date').change( function() {
      var value = jQuery(this).val();
      jQuery("."+jQuery(this).attr("class")).val(value);
    });

    function chnage_columns_names() {
      jQuery('.columns-names-chmages').map(function(){
        var classAttr = jQuery(this).attr('name');
        var new_name = jQuery(this).val();
        if( jQuery('.column-'+classAttr+'.manage-column').hasClass( "sorted" ) ){ 
          jQuery('.column-'+classAttr+'.manage-column').find('span:first').text(new_name);
        } else {
         jQuery('.column-'+classAttr+'.manage-column').text(new_name);
        }
      });
    }
    jQuery('.ys_cfdbh-reset-button').on('click', function( e ) {
      setTimeout(function(){
        jQuery(".ys_cfdbh_end_date").val('');
        jQuery(".ys_cfdbh_start_date").val('');
        jQuery(".ys_cfdbh_end_date").attr({'min':'','max':''});
        jQuery(".ys_cfdbh_start_date").attr({'min':'','max':''});
      }, 200);
    });
    jQuery('.ys_cfdbh_start_date').on('focusout', function() {
      var value = jQuery(this).val();
      if( jQuery(".ys_cfdbh_end_date").val() && jQuery(".ys_cfdbh_end_date").val() < value ){
        jQuery(".ys_cfdbh_end_date").val(value);
      } else if( value ){
         jQuery(".ys_cfdbh_end_date").attr("min",""+value) ;
      }
    });

    jQuery('.ys_cfdbh_end_date').on('focusout', function() {
      var value = jQuery(this).val();
      if( jQuery(".ys_cfdbh_start_date").val() && jQuery(".ys_cfdbh_start_date").val() > value ){
        jQuery(".ys_cfdbh_start_date").val(value);
      } else if( value ){
         jQuery(".ys_cfdbh_start_date").attr("max",""+value) ;
      }
    });

    jQuery('#apply-show-column').trigger('click');
    chnage_columns_names();
});