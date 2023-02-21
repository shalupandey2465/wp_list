// (function( $ ) {
//  'use strict';

//  /**
//   * All of the code for your admin-facing JavaScript source
//   * should reside in this file.
//   *
//   * Note: It has been assumed you will write jQuery code here, so the
//   * $ function reference has been prepared for usage within the scope
//   * of this function.
//   *
//   * This enables you to define handlers, for when the DOM is ready:
//   *
//   * $(function() {
//   *
//   * });
//   *
//   * When the window is loaded:
//   *
//   * $( window ).load(function() {
//   *
//   * });
//   *
//   * ...and/or other possibilities.
//   *
//   * Ideally, it is not considered best practise to attach more than a
//   * single DOM-ready or window-load handler for a particular page.
//   * Although scripts in the WordPress core, Plugins and Themes may be
//   * practising this, we should strive to set a better example in our own work.
//   */

// })( jQuery );



    /**
     * 
     * This function is used to hide and show product id fields
     * 
    */

jQuery(document).ready(function(){

    jQuery('#exampleCheck1').on('click',function(){

        var checkbox=jQuery('#exampleCheck1:checked').val();
        if(checkbox == 'on')
        {
            jQuery('#productId').hide();
        }
        else
        {
            jQuery('#productId').show();
        }
        
    });



});


    /**
     * 
     * This function is used to send ajax in both conditions either we send product ids or not
     * 
    */

jQuery(document).ready(function(){

  jQuery('#wp_List_Example').on('click',function(e){
   var percentComplete=0;
   e.preventDefault();

   var checkbox=jQuery('#exampleCheck1:checked').val();
   if(checkbox=='on')
   {
    jQuery('.show_notice').html('');
    var consumer_key=jQuery('#consumer_key').val();
    var consumer_secret=jQuery('#consumer_secret').val();
    var post_status=jQuery('#product_statuss').val();
    var error_ms='';
    if(consumer_key=='' || consumer_secret=='')
    {
       error_ms='<div class="notice notice-warning is-dismissible my_notice"><p><strong>SUCCESS: </strong>Fields are Empty</p><button type="button" class="notice-dismiss" ><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
       jQuery('.show_notice').html(error_ms);
   }
   else
   {
       jQuery('.show_notice').html('');
       jQuery('#overlayone').html('<div id="overlay"><img id="overlay_img" src="http://localhost/web/practice/wp-content/uploads/2023/02/loading.gif" /></div>');
       jQuery.ajax({

        url  : 'admin-ajax.php',
        dataType: "json",
        type :  'post',
        data :{
           'action'        : 'get_all_product',
           consumer_key    : consumer_key,
           consumer_secret : consumer_secret,
           post_status     : post_status

       },
       success:function(result)
       {
        console.log(result);
        jQuery('#overlayone').html(' ');
        jQuery('.progress').hide();
        var msg='';
        if( result.success = true ) {
            msg = '<div class="notice notice-success is-dismissible my_notice"><p>'+result.message+'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        }
        

        jQuery('.show_notice').html(msg);
        
    } 


});
   }
}
else
{

    var consumer_key=jQuery('#consumer_key').val();
    var consumer_secret=jQuery('#consumer_secret').val();
    var product_id=jQuery('#product_id').val();
    var post_status=jQuery('#product_statuss').val();
    var error_ms='';
    if(consumer_key=='' || consumer_secret=='' || product_id=='')
    {
       jQuery('.show_notice').html('');
       error_ms='<div class="notice notice-warning is-dismissible my_notice"><p><strong>SUCCESS: </strong>Fields are Empty</p><button type="button" class="notice-dismiss" onclick="dissmiss_notice(this);"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
       jQuery('.show_notice').html(error_ms);
   }
   else
   {
      jQuery('.show_notice').html('');
      jQuery('#overlayone').append('<div id="overlay"><img id="overlay_img" src="http://localhost/web/practice/wp-content/uploads/2023/02/loading.gif" /></div>');
      jQuery.ajax({

        url  : 'admin-ajax.php',
        dataType: "json",
        type :  'post',
        data :{
           'action'        : 'get_product_by_id',
           consumer_key    : consumer_key,
           consumer_secret : consumer_secret,
           product_id      : product_id,
           post_status     : post_status

       },
       success:function(result)
       {
        if(result)
        {
            jQuery('#overlayone').html(' ');
        }
        jQuery('.progress').hide();
        var msg='';
        if(result.success== false)
        {
           
           msg = '<div class="notice notice-warning is-dismissible my_notice"><p><strong>SUCCESS: </strong>Product Already Exists</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
           
       }
       else if(result.data == true)
       {
         
        msg = '<div class="notice notice-success is-dismissible my_notice"><p><strong>SUCCESS: </strong>Product Imported successfully</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        
    }

    jQuery('.show_notice').html(msg);

} 

});
  }
}
});


   /**
     * 
     * This function is used to submit the form on onchange of filter type
     * 
    */

  jQuery('.product-type').on('change', function(){
    var product_type = jQuery(this).val();
    if( product_type != '' ){
        jQuery(this).parents('form').submit();
            // document.location.href = 'admin.php?page=product-type'+product_type;    
    }
});

  jQuery('.filter-type').on('change',function(){



   var filter_type = jQuery(this).val();


   if( filter_type != '' ){
    jQuery(this).parents('form').submit();   
}


});


   /**
     * 
     * This function is used to submit the form on click of filter button
     * 
    */

  jQuery('#filter_data').on('click', function(){
    var filter_type_name = jQuery('.perform_onchange').val();

    if( filter_type_name != '' ){

        jQuery('#filter_data').parents('form').submit();   
    }
});




});


