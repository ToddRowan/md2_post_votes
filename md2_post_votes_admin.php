<?php

/* 
 * All the bits for our administration pages.
 */

define("VOTE_OPTIONS_TAG", 'vote_options');

function md2_add_vote_options()
{
    add_options_page( "Grand Rounds configuration", 
                      'Grand Rounds configuration', 
                      'activate_plugins', 
                      VOTE_OPTIONS_TAG, 'md2_generate_grand_rounds_admin');
}

function md2_generate_grand_rounds_admin()
{
    $res = md2_get_all_date_ranges();
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2>Grand Rounds configuration</h2>
        <p>Use this page to administrate the Grand Rounds voting periods and mass emailers.</p>
        <div id="gr_accordion">
            <h3>Post date ranges</h3>
            <div id="date_range_blade">
                <?php md2_date_range_config_blade($res); ?>
            </div>
            <h3>Important dates for this rounds period <span class="important_dates"></span></h3>
            <div id="vote_period_blade">
                <?php md2_vote_period_config_blade($res); ?>
            </div>
            <h3>Voting summary <span class="important_dates"></span></h3>
            <div id="vote_summary_blade">
                <?php md2_vote_results_blade($res); ?>
            </div>
        </div> <!-- accordion -->        
    </div> <!-- div.wrap -->    
    <?php
}

function md2_date_range_config_blade(&$res)
{
    ?>
    <p>Here are the <?php echo count($res); ?> saved date ranges that exist in the system. <span id="add_new_date_range">Add new.</span></p> 
    <?php
        echo "<table id=\"date_range_table\"><tr><th>Select/Edit</th><th>Start</th><th>End</th><th>Status</th><th>Eligible posts</th><th>Activate</th></tr>";
        $selected = false;
        foreach ($res as $date_range)
        {                
            echo "<tr " . (!$selected?'class="selected_row" ':"") . "id=\"dr_row-" . $date_range->id . "\"><td class=\"center\">" . '<div class="select" id="select-'. $date_range->id . '" title="Select this row"></div>';
            echo ($date_range->process_state<=MD2_STATE_NOT_USED?'<div class="edit" id="edit-'. $date_range->id . '" title="Edit post selection dates"></div>':'').'</td>';
            echo "<td><span id=\"start_date-". $date_range->id . '">' . date( 'M j, Y', strtotime($date_range->start_date))."</span></td>";
            echo "<td><span id=\"end_date-". $date_range->id . '">' . date( 'M j, Y', strtotime($date_range->end_date))."</span></td>";
            echo "<td>" . md2_get_state_text($date_range->process_state) . "</td>";
            echo "<td>" . md2_get_total_count_of_posts_by_date_range($date_range->id) . "</td>";
            echo "<td class=\"activate_cell\">" . (md2_is_date_range_activatable($date_range->id)?'<span class="activate" id="activate-' . $date_range->id . '">Activate</span>':"") ."</td>";
            //echo "<td><span class=\"activator" . (md2_is_date_range_activatable($date_range->id)?' activate" ':''). ' id="activate-' . $date_range->id . '">Activate</span></td>';
            echo "<td class=\"data_column\"><div id=\"data-block-" . $date_range->id . "\">";
            echo "<span class=\"is_locked\">{$date_range->is_locked}</span>";
            echo "<span class=\"is_voting_eligible\">{$date_range->is_voting_eligible}</span>";
            echo "<span class=\"vote_mail_sent\">{$date_range->vote_mail_sent}</span>";
            echo "<span class=\"meeting_mail_sent\">{$date_range->meeting_mail_sent}</span>";
            echo "<span class=\"date_vote_email_sent\">" . md2_format_view_date($date_range->date_vote_email_sent). "</span>";
            echo "<span class=\"date_voting_ended\">" . md2_format_view_date($date_range->date_voting_ended) . "</span>";
            echo "<span class=\"date_meet_email_sent\">" . md2_format_view_date($date_range->date_meet_email_sent) . "</span>";
            echo "<span class=\"date_post_selection_ended\">" . md2_format_view_date($date_range->date_post_selection_ended) . "</span>";
            echo "<span class=\"date_of_meet\">" . md2_format_view_date($date_range->date_of_meet) . "</span>";
            echo "<span class=\"time_meet_start\">{$date_range->time_meet_start}</span>";
            echo "<span class=\"time_meet_end\">{$date_range->time_meet_end}</span>";
            echo "<span class=\"phone_number\">{$date_range->phone_number}</span>";
            echo "<span class=\"meeting_id\">{$date_range->meeting_id}</span>";
            echo "<span class=\"meeting_note\">{$date_range->meeting_note}</span>";
            echo "<span class=\"process_state\">{$date_range->process_state}</span>";
            echo "</div><!-- end data block --></td></tr>";
            $selected = true;
        }
        echo "</table>";               
    ?>

    <div id="new_date_range_form">
        <form action="<?php echo plugins_url('md2_vote_options_post.php', __FILE__);?>" method="post" id="md2_date_form">
            <p id="newVoteRange">
                <input id="md2_date_range_start" name="md2_date_range_start" type="text" class="date start" /> <br>to<br>
                <input id="md2_date_range_end" name="md2_date_range_end" type="text" class="date end" />
            </p>
            <button type="submit" id="date_range_submit">Add a new date range</button>&nbsp;<span id="date_range_edit_reset">Cancel</span>
        </form>
    </div>
    <?php
}

function md2_vote_period_config_blade(&$res)
{
    // On edit, set/edit cron job. 
    // UI pieces:
    // No dates
    // Editable dates
    //   Includes Save and activate.
    // Uneditable dates
    ?>
    <p class="vote_not_activated">This period has not been activated.</p>
    
    <p class="voteopen">Voting for this period will begin on <input class="date date_vote_email_sent" name="date_vote_email_sent" type="text">
        and end on <input class="date date_voting_ended" name="date_voting_ended" type="text">.</p>
    <p class="voteclosed">Voting for this period was open from <span class="date_vote_email_sent"></span> 
        to <span class="date_voting_ended"></span>.</p>

    <p class="meetopen">The announcement email for the rounds meeting will be sent on 
        <input class="date date_meet_email_sent" name="date_meet_email_sent" type="text">.</p>
    <p class="meetclosed">The announcement email for this period was sent on <span class="date_meet_email_sent"></span>.</p>
    
    <p class="meetopen">The meeting will be on <input class="date date_of_meet" name="date_of_meet" type="text">
        from <input class="time time_meet_start" name="time_meet_start" type="text"> to 
        <input class="time time_meet_end" name="time_meet_end" type="text"> (Pacific Time).</p>
    
    <p class="meetclosed">The meeting was scheduled for <span class="date_of_meet"></span>
        from <span class="time_meet_start"></span> to <span class="time_meet_end"></span>.</p>        
        
    <p class="meetopen">Callers will dial <input class="phone_number" name="phone_number" type="text"> and use the code <input class="meeting_id" name="meeting_id" type="text">.</p>
    <p class="meetclosed">Callers were instructed to dial <span class="phone_number"></span> and use the code <span class="meeting_id"></span>.</p>

    <p class="meetopen">Any additional info or special instructions? <input class="meeting_note" name="meeting_note" type="text"></p>
    <p class="meetclosed">Callers were instructed to also do the following: <span class="meeting_note"></span></p>
    
    <input type="hidden" name="dr_id" id="dr_id">
    <input type="hidden" name="act" id="activate_state" value="update">
    <input type="button" title="Activate" value="Activate" id="activate_button">
    
    <?php
}

function md2_vote_results_blade(&$res)
{
  foreach ($res as $date_range)
  {
    echo "<div class=\"vote_result_group\" id=\"date_range_votes-{$date_range->id}\">";
    switch($date_range->process_state)
    {
      case MD2_STATE_NOT_USED: // Created but not used.
      case MD2_STATE_ACTIVATED: //Activated, waiting to send vote start email. 
        echo "<p>Voting for this period has not yet begun.";
        break;
      
      case MD2_STATE_VOTE_COMPLETED:  //Activated, voting over, waiting to send meeting email
      case MD2_STATE_MEET_MAIL_SENT: //Activated, voting over, meeting email and invite sent
      case MD2_STATE_ARCHIVED:  //Archived, meeting date passed.
      case MD2_STATE_VOTE_MAIL_SENT:  //Activated, vote email sent, voting open.
        echo md2_output_vote_status_msg($date_range);
        
        echo "<h3>Votes by post</h3>";
        md2_output_votes_by_post($date_range->id);
        echo "<p>Note that eligible posts without votes are not included in the above table.</p>";
        
        echo "<h3>Votes by user</h3>";
        md2_output_votes_by_doctor($date_range->id);      
    }
  ?>
    <?php
    echo "</div> <!-- date_range_votes-{$date_range->id} -->";
  }
}

function md2_output_votes_by_doctor($id)
{
  $votes = md2_get_user_vote_counts($id);
  
  if (count($votes)==0)
  {
    echo "<p>No results at this time.</p>";
  }
 else 
 {
    $novotes = md2_get_users_without_votes($id);
    echo "<table class=\"vote_results\"><tr><th>Doctor</th><th>Total votes</th></tr>";

    foreach ($votes as $vote)
    {
      echo "<tr><td>".$vote->user_login."</td><td>".$vote->votecount."</td></tr>";
    }
    foreach ($novotes as $novote)
    {
      echo "<tr><td>".$novote->user_login."</td><td>0</td></tr>";
    }

    echo "</table>";
 }
}

function md2_output_votes_by_post($id)
{
  $votes = md2_get_post_vote_counts($id);
  
  if (count($votes)==0)
  {
    echo "<p>No results at this time.</p>";
  }
 else 
 {
    echo "<table class=\"vote_results\"><tr><th>Post</th><th>Total votes</th></tr>";

    foreach ($votes as $vote)
    {
      echo "<tr><td>".$vote->post_title."</td><td>".$vote->votecount."</td></tr>";
    }

    echo "</table>";
 }
}

function md2_output_vote_status_msg($dr)
{
  $msg = "";
  switch($dr->process_state)
  {
    case MD2_STATE_VOTE_COMPLETED:  //Activated, voting over, waiting to send meeting email
    case MD2_STATE_MEET_MAIL_SENT: //Activated, voting over, meeting email and invite sent
    case MD2_STATE_ARCHIVED:  //Archived, meeting date passed.
      $msg = "<p>Voting is complete.</p>";
      break;
    case MD2_STATE_VOTE_MAIL_SENT:  //Activated, vote email sent, voting open.
      $d = date_create("now", md2_get_default_tz());
      $msg = "<p>Voting is active. Vote counts are current as of " . $d->format("l, F jS") . " at ". $d->format("g:i a") . " pacific time.</p>";
  }
  
  return $msg;
}

function md2_add_grand_rounds_scripts()
{
    if (isset($_GET['page']) && $_GET['page']==='vote_options')
    {

      wp_register_style('jqui-style', plugins_url('jqui/css/flick/jquery-ui-1.10.4.custom.min.css', __FILE__));
      wp_register_style('jq_timepicker', plugins_url('jqui/css/jquery.timepicker.css', __FILE__));
      wp_register_style('md2-votes-admin-style', plugins_url('css/votes_admin.css', __FILE__), array('jqui-style','jq_timepicker'));
      wp_register_script('jqui', plugins_url('jqui/jquery-ui-1.10.4.custom.min.js', __FILE__), array('jquery'));
      wp_register_script('jq_time_picker', plugins_url('jqui/jquery.timepicker.min.js', __FILE__), array('jquery'));
      wp_register_script('md2_vote_tools_init', plugins_url('jqui/voting_tools_init.js', __FILE__), array('jqui','jq_time_picker'));

      wp_enqueue_style('md2-votes-admin-style');
      wp_enqueue_script('jq_time_picker');
      wp_enqueue_script('md2_vote_tools_init');
    }
}

function md2_format_view_date($d)
{
    if (!is_null($d))
    {
        return date('M j, Y', strtotime($d));
    }
    else
    {
        return "";
    }
}

function md2_get_state_text($state)
{
    $txt = "";
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
            break;            
    }
    
    return $txt;
}


add_action('admin_menu', 'md2_add_vote_options');
add_action('admin_enqueue_scripts', 'md2_add_grand_rounds_scripts');