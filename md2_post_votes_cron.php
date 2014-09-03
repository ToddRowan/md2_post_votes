<?php

// Cron job files

define("MD2_CRON_TZ", "America/Los_Angeles");
define("MD2_VOTE_RELATED_CRON", 'md2_vote_related_cron');
// The cron-able action must be outside of the function that crons it! Duh.
add_action(MD2_VOTE_RELATED_CRON,'md2_do_vote_cron',10,1);

function md2_get_default_tz()
{
  return new DateTimeZone(MD2_CRON_TZ);
}

function md2_add_single_cron_datetime($id, $dt)
{
  md2_add_single_cron($id, date_format($dt, 'U'));
}

function md2_add_single_cron($id, $ts)
{
  wp_schedule_single_event( $ts, MD2_VOTE_RELATED_CRON, array($id));
}

function md2_do_vote_cron($id)
{
  md2_cron_log("\nStarting vote cron work\n");
//$id is of date range  
  $dr = md2_get_vote_date_range_by_id($id);
  $upd_fields = null;
  $cron_date = null;
  $dtz = md2_get_default_tz();
  
  // Look at the current state and do what it takes to complete the next state.
  switch($dr->process_state)
  {
    case MD2_STATE_NOT_USED: // Created but not used.
      // Do nothing, I think.
      break;
    case MD2_STATE_ACTIVATED: //Activated, waiting to send vote start email. 
      md2_cron_log("Activated state\n");
      // set cron job at midnight on the vote end date to disable voting.       
      $tmp_date = date_create($dr->date_voting_ended,$dtz);
      $cron_date = clone $tmp_date;
      $cron_date->modify("+24 hours");  
      md2_cron_log("Populating the posts table for voting\n");
      // Collect the set of eligible posts
      md2_populate_eligible_posts_by_date_range($id);
      // Send the email that voting is open.
      md2_cron_log("Sending emails to doctors\n");
      md2_send_vote_start_mail($dr);
      // set voting eligible to y, advance the process state
      $upd_fields = array("vote_mail_sent"=>'y', "is_voting_eligible"=>'y', "process_state"=>MD2_STATE_VOTE_MAIL_SENT);
      break;
    case MD2_STATE_VOTE_MAIL_SENT:  //Activated, vote email sent, voting open.
      // Disable the voting and the voting page
      // Set the cron job to send the meeting email
      $tmp_date = date_create($dr->date_meet_email_sent,$dtz);
      $cron_date = clone $tmp_date;
      $cron_date->modify("+6 hours");        
      md2_cron_log("Disabling voting\n");
      
      md2_cron_log("Setting list of eligible posts\n");
      md2_set_eligible_posts($dr->id);
      // Advance the process state
      $upd_fields = array("is_voting_eligible"=>'n', "process_state"=>MD2_STATE_VOTE_COMPLETED);
      break;
    case MD2_STATE_VOTE_COMPLETED:  //Activated, voting over, waiting to send meeting email
      // generate the ics file      
      // Send email about meeting date.
      md2_cron_log("Sending reminder email about the meeting\n");
      md2_send_meeting_mail($dr);
      //set cron job to archive the results at meeting time. 
      md2_cron_log("Setting cron to archive the vote results\n");
      $tmp_date = date_create($dr->date_of_meet,$dtz);
      $meet_time_arr = explode(":",$dr->time_meet_start);
      $meet_time_start = intval($meet_time_arr[0]);
      $cron_date = clone $tmp_date;
      $cron_date->modify("+$meet_time_start hours");        
      
      // Advance process state
      $upd_fields = array("meeting_mail_sent"=>'y', "process_state"=>MD2_STATE_MEET_MAIL_SENT);
      break;
    case MD2_STATE_MEET_MAIL_SENT:  //Activated, voting over, meeting email and invite sent           
      // Advance process state
      md2_cron_log("Marking the date range as archived\n");
      $upd_fields = array("process_state"=>MD2_STATE_ARCHIVED);
      break;
    case MD2_STATE_ARCHIVED: 
      // Do nothing
      break;
  }
  
  if (!is_null($cron_date))
  {
    md2_cron_log("Setting the new cron date\n");
    //md2_add_single_cron_datetime($id, $cron_date);
  }
  if (!is_null($upd_fields))
  {
    md2_cron_log("Updating the date range record\n");
    md2_update_vote_date_range($id, $upd_fields);
  }
}


function md2_cron_log($msg, $test_mode = true)
{
    if ($test_mode == true )
    {
        error_log($msg, 3, "/var/www/html/portal/images_upload/votes_cron_log.txt");
    }
}
?>