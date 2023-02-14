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


jQuery(document).ready(function(){

    jQuery('#exampleCheck1').on('click',function(){

        var checkbox=jQuery('#exampleCheck1:checked').val();
        console.log(checkbox);
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

jQuery(document).ready(function(){
  jQuery('#wp_List_Example').on('click',function(e){

         e.preventDefault();
        var checkbox=jQuery('#exampleCheck1:checked').val();
        console.log(checkbox);
        if(checkbox=='on')
        {

            var consumer_key=jQuery('#consumer_key').val();
            var consumer_secret=jQuery('#consumer_secret').val();
            jQuery.ajax({

                url  : 'admin-ajax.php',
                type :  'post',
                data :{
                         'action'        : 'get_all_product',
                         consumer_key    : consumer_key,
                         consumer_secret : consumer_secret

                      },
                      sucees:function(result){

                      }

              });
        }
        else
        {

            var consumer_key=jQuery('#consumer_key').val();
            var consumer_secret=jQuery('#consumer_secret').val();
            var product_id=jQuery('#product_id').val();
            jQuery.ajax({

                url  : 'admin-ajax.php',
                type :  'post',
                data :{
                         'action'        : 'get_product_by_id',
                         consumer_key    : consumer_key,
                         consumer_secret : consumer_secret,
                         product_id      : product_id

                      },
                      sucees:function(result){

                      }

            });
        }
        
        
        

    });

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

     jQuery('#filter_data').on('click', function(){
        var filter_type_name = jQuery('.perform_onchange').val();
        console.log(filter_type_name);
        if( filter_type_name != '' ){

            jQuery('#filter_data').parents('form').submit();
            // document.location.href = 'admin.php?page=product-type'+product_type;    
        }
    });

  });


