jQuery(document).ready( function() {
    jQuery('#md2_date_range_start').Zebra_DatePicker({
        format: "M j, Y", pair:jQuery('#md2_date_range_end'),offset:[5,150],direction:false
    });
    //http://stefangabos.ro/jquery/zebra-datepicker/
    jQuery('#md2_date_range_end').Zebra_DatePicker({
        format: "M j, Y",offset:[5,100]
    });
    
    jQuery('.edit').click(
            function(evt)
            {
                setupDateEdit(evt);
            });
    
    jQuery('#date_range_edit_reset').click(
            function(evt)
            {
                resetDateEdit();
            });
            
});

function setupDateEdit(evt)
{
    var $el = jQuery(evt.currentTarget);
    var id = $el.attr('id').split('-')[1];
    var $form = jQuery('#md2_date_form');
    
    var $input = jQuery('#edit_date_range');
    if ($input.length==0)
    {
        $input = $form.append('<input type="hidden" id="edit_date_range" name="edit_date_range" value="' + id + '">');
    }
    else
    {
        $input.val(id);
    }
    
    jQuery("#md2_date_range_start").val(jQuery("#start_date-"+id).text());
    jQuery("#md2_date_range_end").val(jQuery("#end_date-"+id).text());
    jQuery("#date_range_submit").text("Edit this date range");
    jQuery('#date_range_edit_reset').show();
}

function resetDateEdit()
{
    var $input = jQuery('#edit_date_range').val(-1);
    jQuery("#md2_date_range_start, #md2_date_range_end").val("");
    jQuery("#date_range_submit").text("Add a new date range");
    jQuery('#date_range_edit_reset').hide();
}