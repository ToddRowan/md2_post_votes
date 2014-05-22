var md2_data_blocks = {};
jQuery(document).ready( function() {

    jQuery( "#gr_accordion" ).accordion({collapsible: true, heightStyle: "content"}); 
    jQuery( ".date" ).datepicker({dateFormat: "M d, yy"});
    jQuery('.time').timepicker({'minTime': '7:00am', 'maxTime': '7:00pm'}); //http://jonthornton.github.io/jquery-timepicker/
    
    jQuery('#add_new_date_range').click(
            function(evt)
            {
                setupDateEdit(evt);
            });
            
    jQuery('#date_range_table').on('click', 'span.edit', function(evt)
            {
                setupDateEdit(evt);
            });
            
    jQuery('#date_range_edit_reset').click(
            function(evt)
            {
                resetDateEdit();
            });    
           
    jQuery('#date_range_table').on('click', 'span.activate', function(evt)
            {
                activatePeriod(evt);
            });
            
    jQuery('#date_range_table').on('click', 'div.select', function(evt)
            {
                changeSelection(evt);
            });
    
    populateAccordion();
});

function setupDateEdit(evt)
{
    var $el = jQuery(evt.currentTarget);
    var id = $el.attr('id');
    if (id!=='add_new_date_range')
    {
        id = id.split('-')[1];
    }
    else
    {
        id = -1;
    }
    
    populateDateEdit(id);
    
    var $formBox = jQuery('#new_date_range_form');
    $formBox.show();
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
    
    jQuery('#date_range_edit_reset').show();
}

function populateDateEdit(id)
{
    jQuery("#md2_date_range_start").val(jQuery("#start_date-"+id).text());
    jQuery("#md2_date_range_end").val(jQuery("#end_date-"+id).text());
    jQuery("#date_range_submit").text((id==-1?"Add a new date range":"Edit this date range")); 
    jQuery("#md2_date_form").submit(function(){doDateRangeEdit();return false;})
}

function resetDateEdit()
{
    var $input = jQuery('#edit_date_range').val(-1);
    jQuery("#md2_date_range_start, #md2_date_range_end").val("");
    jQuery("#date_range_submit").text("Add a new date range");
    hideDateEditForm();
}

function hideDateEditForm()
{
    jQuery('#date_range_edit_reset, #new_date_range_form').hide();
}

function activatePeriod(evt)
{
    jQuery('#vote_period_blade').prev('h3').first().click();
}

function changeSelection(evt)
{
    jQuery('.selected_row').removeClass('selected_row');
    jQuery(evt.currentTarget).closest('tr').addClass('selected_row');
    populateAccordion();
    // populate data in the other blades
}

function populateAccordion()
{
    // First, set the date on the accordion header.
    var id = jQuery(".selected_row").first().attr('id').split("-")[1];
    var start = jQuery('#start_date-'+id).text();
    var end = jQuery('#end_date-'+id).text();
    jQuery('.important_dates').text("(" + start + " - " + end + ")");
    
    // Next, populate the accordion content items. 
    configureVoteDates(id);
}

function configureVoteDates(id)
{
    /*Dates can be edited - is locked == 'n' && is_voting_eligible == 'n' && vote_mail_sent == 'n'
    Dates cannot be edited -- is locked == 'y' || is_voting_eligible == 'y' || vote_mail_sent == 'y'
    Users can vote - voting is active -- is_voting_eligible == 'y'
    Users cannot vote - voting is not active == is_voting_eligible == 'n'
    Admin can make selections - is the same as above? -- is_voting_eligible == 'n' && meeting_mail_sent == 'n'
    No one can change anything - is locked == 'y' */
    var data = populateDateObj(id);
    populateDataFields(data);
    
    if (data.is_locked=="y") 
    {
        // Everything is shut
        jQuery(".voteopen, .meetopen").hide();
        jQuery(".voteclosed, .meetclosed").show();
        return;
    }    
    
    if (votingDatesEditable(data))
    {
        // Add the calendar items for the voting dates
        jQuery(".voteopen").show();
        jQuery(".voteclosed").hide();
    }
    else
    {
        jQuery(".voteclosed").show();
        jQuery(".voteopen").hide();
    }
    
    if (meetingInfoEditable(data))
    {
        // Add the calendar items for the voting dates
        jQuery(".meetopen").show();
        jQuery(".meetclosed").hide();
    }
    else
    {
        jQuery(".meetclosed").show();
        jQuery(".meetopen").hide();
    }
}

function votingDatesEditable(data)
{
    if (data.is_locked == 'n' && data.is_voting_eligible == 'n' && data.vote_mail_sent == 'n')
        return true;
    if( data.is_locked == 'y' || data.is_voting_eligible == 'y' || data.vote_mail_sent == 'y')
        return false;
}

function meetingInfoEditable(data)
{
    return true;
}

function populateDateObj(id)
{
    if (md2_data_blocks.hasOwnProperty(id))
    {
        return md2_data_blocks[id];
    }
    else
    {
        var $blockSrc = jQuery("#data-block-"+id);

        var obj = {
            "id": id,
            is_locked: $blockSrc.find(".is_locked").first().text(),
            is_voting_eligible: $blockSrc.find(".is_voting_eligible").first().text(),
            vote_mail_sent: $blockSrc.find(".vote_mail_sent").first().text(),
            meeting_mail_sent: $blockSrc.find(".meeting_mail_sent").first().text(),
            date_vote_email_sent: $blockSrc.find(".date_vote_email_sent").first().text(),
            date_voting_ended: $blockSrc.find(".date_voting_ended").first().text(),
            date_meet_email_sent: $blockSrc.find(".date_meet_email_sent").first().text(),
            date_post_selection_ended: $blockSrc.find(".date_post_selection_ended").first().text(),
            date_of_meet: $blockSrc.find(".date_of_meet").first().text(),
            time_meet_start: $blockSrc.find(".time_meet_start").first().text(),
            time_meet_end: $blockSrc.find(".time_meet_end").first().text(),
            phone_number: $blockSrc.find(".phone_number").first().text(),
            meeting_id: $blockSrc.find(".meeting_id").first().text(),
            meeting_note: $blockSrc.find(".meeting_note").first().text()
        };
        
        md2_data_blocks[id]= obj;
        return obj;
    }
}

function populateDataFields(data)
{
    var $blockTgt = jQuery("#vote_period_blade");

    $blockTgt.find(".vote_mail_sent").each(function(i,e){textOrVal(data.vote_mail_sent,e)});
    $blockTgt.find(".meeting_mail_sent").each(function(i,e){textOrVal(data.meeting_mail_sent,e)});
    $blockTgt.find(".date_vote_email_sent").each(function(i,e){textOrVal(data.date_vote_email_sent,e)});
    $blockTgt.find(".date_voting_ended").each(function(i,e){textOrVal(data.date_voting_ended,e)});
    $blockTgt.find(".date_meet_email_sent").each(function(i,e){textOrVal(data.date_meet_email_sent,e)});
    $blockTgt.find(".date_post_selection_ended").each(function(i,e){textOrVal(data.date_post_selection_ended,e)});
    $blockTgt.find(".date_of_meet").each(function(i,e){textOrVal(data.date_of_meet,e)});
    $blockTgt.find(".time_meet_start").each(function(i,e){textOrVal(convertTime(data.time_meet_start),e)});
    $blockTgt.find(".time_meet_end").each(function(i,e){textOrVal(convertTime(data.time_meet_end),e)});
    $blockTgt.find(".phone_number").each(function(i,e){textOrVal(data.phone_number,e)});
    $blockTgt.find(".meeting_id").each(function(i,e){textOrVal(data.meeting_id,e)});
    $blockTgt.find(".meeting_note").each(function(i,e){textOrVal(data.meeting_note,e)});
}

function convertTime(t)
{
    if (t=="")return "";
    var t_arr = t.split(":");
    var pm = parseInt(t_arr[0])>11?"pm":"am";
    var h = parseInt(t_arr[0])>12?parseInt(t_arr[0])-12:t_arr[0];
    return h+":"+t_arr[1]+pm;
}

function textOrVal(txt,e)
{
    if (jQuery(e).is('input'))
        jQuery(e).val(txt);
    else
        jQuery(e).text(txt);
}

function doDateRangeEdit()
{
    var data = {
            action: 'md2_edit_date_range',
            id: jQuery('#edit_date_range').val(),                
            new_start: jQuery('#md2_date_range_start').val(),
            new_end: jQuery('#md2_date_range_end').val()
        };
                
    jQuery.post(ajaxurl,
        data, function(response) {
                        handleDateRangeEdit(response);
                },"json");
}

function handleDateRangeEdit(s)
{
    var id = s.id;
    
    var nstart = jQuery('#md2_date_range_start').val();
    var nend = jQuery('#md2_date_range_end').val();
    
    var $row = jQuery('#dr_row-'+id);
    
    if ($row.length>0)
    {
        jQuery('#start_date-'+id).text(nstart);
        jQuery('#end_date-'+id).text(nend);        
    }
    else
    {
        // insert row into top of table (how do I sort?)
        insertNewDateRange(s,nstart,nend);
    }
    
    hideDateEditForm();
    populateAccordion();
}

function insertNewDateRange(newobj, newstart, newend)
{
    var $table = jQuery('#date_range_table');
    $table.find('tr').first().after(
            "<tr id=\"dr_row-" + newobj.id + "\"><td class=\"center\"><div class=\"select\" id=\"select-" + newobj.id + "\"></div></td>"+ // Selected
            "<td><span id=\"start_date-" + newobj.id + "\">"+newstart+"</span></td>" + //start
            "<td><span id=\"end_date-" + newobj.id + "\">"+newend+"</span></td>" + //end
            "<td>Not activated</td>" + //status
            "<td>" + newobj.post_count +"</td>" + //post_count
            "<td>" + (newobj.activatable?"<span class=\"activate\" id=\"activate-" + newobj.id + "\">Activate</span>":"") + "</td>" + //activate
            "<td><span class=\"edit\" id=\"edit-" + newobj.id + "\">Edit</span></td>" + // Edit dates
            insertDataColumn(newobj) + "</tr>"
            );
}

function insertDataColumn(obj)
{
    return '<td class="data_column">'+
            '<div id="data-block-' + obj.id + '">' +
            '<span class="is_locked">n</span><span class="is_voting_eligible">n</span>'+
            '<span class="vote_mail_sent">n</span><span class="meeting_mail_sent">n</span>'+
            '<span class="date_vote_email_sent"></span><span class="date_voting_ended"></span>'+
            '<span class="date_meet_email_sent"></span><span class="date_post_selection_ended"></span>'+
            '<span class="date_of_meet"></span><span class="time_meet_start"></span>'+
            '<span class="time_meet_end"></span><span class="phone_number"></span>'+
            '<span class="meeting_id"></span><span class="meeting_note"></span></div><!-- end data block --></td>';
}