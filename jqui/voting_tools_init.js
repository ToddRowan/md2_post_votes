var md2_data_blocks = {};
var $ = $ || jQuery;

var MD2_STATE_NOT_USED=1*256;
var MD2_STATE_ACTIVATED=2*256;
var MD2_STATE_VOTE_MAIL_SENT=3*256;
var MD2_STATE_VOTE_COMPLETED=4*256;
var MD2_STATE_MEET_MAIL_SENT=5*256;
var MD2_STATE_ARCHIVED=6*256;

$(document).ready( function() {
    $("#gr_accordion").accordion({collapsible: true, heightStyle: "content"}); 
    $(".date").datepicker({dateFormat: "M d, yy"});
    $('.time').timepicker({'minTime': '7:00am', 'maxTime': '7:00pm'}); //http://jonthornton.github.io/jquery-timepicker/
    $("#md2_date_form").submit(function(){doDateRangeEdit();return false;});
    
    $('#add_new_date_range').click(
            function(evt)
            {
                setupDateEdit(evt);
            });
            
    $('#date_range_table').on('click', 'div.edit', function(evt)
            {
                setupDateEdit(evt);
            });
            
    $('#date_range_edit_reset').click(
            function(evt)
            {
                resetDateEdit();
            });    
           
    $('#date_range_table').on('click', 'span.activate', function(evt)
            {
                activatePeriod(evt);
            });
            
    $('#date_range_table').on('click', 'div.select', function(evt)
            {
                changeSelection(evt);
            });
            
    $('#activate_button').click(
            function(evt)
            {
                doDrActivate(evt);
            });
    
    populateAccordion();
});

function setupDateEdit(evt)
{
    var $el = $(evt.currentTarget);
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
    
    var $formBox = $('#new_date_range_form');
    $formBox.show();
    var $form = $('#md2_date_form');
    
    var $input = $('#edit_date_range');
    if ($input.length==0)
    {
        $input = $form.append('<input type="hidden" id="edit_date_range" name="edit_date_range" value="' + id + '">');
    }
    else
    {
        $input.val(id);
    }    
    
    $('#date_range_edit_reset').show();
}

function populateDateEdit(id)
{
    $("#md2_date_range_start").val($("#start_date-"+id).text());
    $("#md2_date_range_end").val($("#end_date-"+id).text());
    $("#date_range_submit").text((id==-1?"Add a new date range":"Edit this date range")); 
}

function resetDateEdit()
{
    var $input = $('#edit_date_range').val(-1);
    $("#md2_date_range_start, #md2_date_range_end").val("");
    $("#date_range_submit").text("Add a new date range");
    hideDateEditForm();
}

function hideDateEditForm()
{
    $('#date_range_edit_reset, #new_date_range_form').hide();
}

function activatePeriod(evt)
{
    $(".voteopen, .meetopen").show();
    $(".voteclosed, .meetclosed, .vote_not_activated").hide();
    setActivateButton(true, "Activate");
    $('#vote_period_blade').prev('h3').first().click();
}

function changeSelection(evt)
{
    $('.selected_row').removeClass('selected_row');
    $(evt.currentTarget).closest('tr').addClass('selected_row');
    populateAccordion();
    // populate data in the other blades
}

function populateAccordion()
{
    // First, set the date on the accordion header.
    var id = $(".selected_row").first().attr('id').split("-")[1];
    var start = $('#start_date-'+id).text();
    var end = $('#end_date-'+id).text();
    $('.important_dates').text("(" + start + " - " + end + ")");
    
    // Next, populate the accordion content items. 
    configureVoteDates(id);
}

function configureVoteDates(id)
{
    var data = populateDateObj(id);
    populateDataFields(data);
    
    if (data.process_state>=MD2_STATE_MEET_MAIL_SENT/*MD2_STATE_ARCHIVED*/) 
    {
        // Everything is shut
        $(".voteopen, .meetopen, .vote_not_activated").hide();
        $(".voteclosed, .meetclosed").show();
        setActivateButton(false);
        return;
    }
    
    if (data.process_state==MD2_STATE_NOT_USED)
    {
      $(".voteopen, .meetopen,.voteclosed, .meetclosed").hide();  
      $('.vote_not_activated').show();
      setActivateButton(false);
      return;
    }
    else
    {
      $('.vote_not_activated').hide();
    }
    
    if (data.process_state==MD2_STATE_ACTIVATED)
    {
      $(".voteopen, .meetopen").show();
      $(".voteclosed, .meetclosed").hide();
      setActivateButton(true, "Update");
      return;
    }
    
    if (data.process_state==MD2_STATE_VOTE_MAIL_SENT || data.process_state==MD2_STATE_VOTE_COMPLETED)
    {
      $(".voteclosed, .meetopen").show();
      $(".voteopen, .meetclosed").hide();
      setActivateButton(true, "Update");
      return;
    }
}

function setActivateButton(vis, txt)
{
  if (txt)
  {
    $('#activate_button').val(txt);
    $('#activate_state').val(txt.toLowerCase()); 
  }
  if (!vis)
    $('#activate_button').hide();
  else
    $('#activate_button').show();
}

function votingDatesEditable(data)
{
    return (data.process_state < MD2_STATE_VOTE_MAIL_SENT);
}

function meetingInfoEditable(data)
{
    return (data.process_state < MD2_STATE_MEET_MAIL_SENT);
}

function populateDateObj(id)
{
    if (md2_data_blocks.hasOwnProperty(id))
    {
        return md2_data_blocks[id];
    }
    else
    {
        var $blockSrc = $("#data-block-"+id);

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
            meeting_note: $blockSrc.find(".meeting_note").first().text(),
            process_state: parseInt($blockSrc.find(".process_state").first().text())
        };
        
        md2_data_blocks[id]= obj;
        return obj;
    }
}

function updateDateObj(o)
{
  var id = o.id;
  var tgt = populateDateObj(id);
  
  for (var key in o) {
      if(o.hasOwnProperty(key) && key!='id')
      {
        tgt[key]=o[key];
      }
   }
}

function populateDataFields(data)
{
    var $blockTgt = $("#vote_period_blade");

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
    $blockTgt.find(".process_state").each(function(i,e){textOrVal(data.meeting_note,e)});
    $blockTgt.find("#dr_id").each(function(i,e){textOrVal(data.id,e)});
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
    if ($(e).is('input'))
        $(e).val(txt);
    else
        $(e).text(txt);
}

function doDateRangeEdit(evt)
{
    var data = {
            action: 'md2_edit_date_range',
            id: $('#edit_date_range').val(),                
            new_start: $('#md2_date_range_start').val(),
            new_end: $('#md2_date_range_end').val()
        };
                
    $.post(ajaxurl,
        data, function(response) {
                        handleDateRangeEdit(response);
                },"json");
}

function handleDateRangeEdit(s)
{
    var id = s.id;
    
    var nstart = $('#md2_date_range_start').val();
    var nend = $('#md2_date_range_end').val();
    
    var $row = $('#dr_row-'+id);
    
    if ($row.length>0)
    {
        $('#start_date-'+id).text(nstart);
        $('#end_date-'+id).text(nend); 
        insertActivationSpan(s,$row);
    }
    else
    {
        // insert row into top of table (how do I sort?)
        insertNewDateRange(s,nstart,nend);
    }
    
    $('.selected_row').removeClass('selected_row');
    $("#dr_row-" + id).addClass('selected_row');
    
    hideDateEditForm();
    populateAccordion();
}

function insertActivationSpan(data,$row)
{
  if (!data.activatable)
  {
    // not activatable, hide any span that may be visible
    $row.find(".activate_cell span.activate").hide();
  }
  else
  {
    // if activatable and the span exists, show it
    if ($row.find(".activate_cell span.activate").length>0)
    {
        $row.find(".activate_cell span.activate").show();
    }
    else
    {
      // otherwise we have to add it. 
      $row.find(".activate_cell").first().html(getActivationSpan(data.id));
    }
  }
}

function getActivationSpan(id)
{
  return "<span class=\"activate\" id=\"activate-" + id + "\">Activate</span>";
}

function insertNewDateRange(newobj, newstart, newend)
{
    var $table = $('#date_range_table');
    $table.find('tr').first().after(
            "<tr id=\"dr_row-" + newobj.id + "\"><td class=\"center\"><div class=\"select\" id=\"select-" + newobj.id + "\"></div>" +
            "<div class=\"edit\" id=\"edit-" + newobj.id + "\"></div></td>"+ // Selected/edit
            "<td><span id=\"start_date-" + newobj.id + "\">"+newstart+"</span></td>" + //start
            "<td><span id=\"end_date-" + newobj.id + "\">"+newend+"</span></td>" + //end
            "<td>" + getStateText(newobj.process_state) + "</td>" + //status
            "<td>" + newobj.post_count +"</td>" + //post_count
            "<td class=\"activate_cell\">" + (newobj.activatable?getActivationSpan(newobj.id):'') + "</td>" + //activate
            insertDataColumn(newobj) + "</tr>"
            );
    
    // if this row is new, select it
    
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
            '<span class="meeting_id"></span><span class="meeting_note"></span>'+
            '<span class="process_state">' + obj.process_state + '</span></div><!-- end data block --></td>';
}

function getStateText($state)
{
    var $txt = "";
    switch($state)
    {
        case MD2_STATE_NOT_USED:
            $txt = "Unused";
            break;
        case MD2_STATE_ACTIVATED:
            $txt = "Active, not started";
            break;
        case MD2_STATE_VOTE_MAIL_SENT:
            $txt = "Voting active";
            break;
        case MD2_STATE_VOTE_COMPLETED:
            $txt = "Voting completed";
            break;
        case MD2_STATE_MEET_MAIL_SENT:
            $txt = "Meeting invite sent";
            break;
        case MD2_STATE_ARCHIVED:
            $txt = "Archived";           
    }    
    return $txt;
}

function doDrActivate(evt)
{
  var $activateSrc = $('#vote_period_blade');
  var optionalFields = ['date_meet_email_sent', "date_of_meet","time_meet_start","time_meet_end",
    "phone_number","meeting_id","meeting_note","act"];
  
  var data = {
    action: 'md2_activate_date_range',
    id: $activateSrc.find('#dr_id').first().val(),
    date_vote_email_sent: $activateSrc.find('input[name="date_vote_email_sent"]').first().val(),
    date_voting_ended: $activateSrc.find('input[name="date_voting_ended"]').first().val(),
    act: $activateSrc.find('input[name="act"]').first().val()
  };
    
  for (p in optionalFields)
  {
    var $prop = $activateSrc.find('input[name="' + optionalFields[p] + '"]').first();
    if ($prop.length == 1)
    {
      data[optionalFields[p]]=$prop.val();
    }    
  }
  
  if (data.date_vote_email_sent == "" || data.date_voting_ended == "")
  {
    // problems
    alert('Please select start and end dates for the voting period.');
    return;
  }
  
  $.post(ajaxurl,
        data, function(response) {
                        handleDateRangeActivate(response);
                },"json");
}

function handleDateRangeActivate(response)
{
  // update the returned fields and repopulate the accordion.
  
  // Change the button and update stuff text.
  if (response.error == 1)
  {
    //oops
    alert(response.msg);
  }
  else
  {
    document.location.reload(true);
    //updateDateObj(response.obj);
    //configureVoteDates(response.obj.id)
    // update the data store with the items from the request (or forms)
    
    // update the form blade
    
    // update the table
  }
}