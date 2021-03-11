(function($){
"use strict";
    
    var contenttypeval = admin_wclocalize_data.contenttype;
    if( contenttypeval == 'fakes' ){
        $(".notification_fake").show();
        $(".notification_real").hide();
    }else{
        $(".notification_fake").hide();
        $(".notification_real").show();
    }
    // When Change radio button
    $(".notification_content_type .radio").on('change',function(){
        if( $(this).is(":checked") ){
            contenttypeval = $(this).val();
        }
        if( contenttypeval == 'fakes' ){
            $(".notification_fake").show();
            $(".notification_real").hide();
        }else{
            $(".notification_fake").hide();
            $(".notification_real").show();
        }
    });

    // Fakes data Reapeter Field Increase
    $( '#add-row' ).on('click', function() {
        var row = $( '.empty-row.screen-reader-text' ).clone(true);
        row.removeClass( 'empty-row screen-reader-text' );
        row.insertBefore( '#htrepeatable-fieldset tbody>tr:last' );
        return false;
    });

    // Fakes data Reapeter Field Decrease
    $( '.remove-row' ).on('click', function() {
        $(this).parents('tr').remove();
        return false;
    });


    
})(jQuery);