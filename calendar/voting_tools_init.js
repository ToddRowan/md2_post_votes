jQuery(document).ready( function() {
    jQuery('#md2_date_range_start').Zebra_DatePicker({
        format: "M j, Y", pair:jQuery('#md2_date_range_end'),offset:[5,150],direction:false
    });
    jQuery('#md2_date_range_end').Zebra_DatePicker({
        format: "M j, Y", direction:1,offset:[5,100]
    });  
});
//http://stefangabos.ro/jquery/zebra-datepicker/