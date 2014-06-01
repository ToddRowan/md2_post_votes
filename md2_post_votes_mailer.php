<?php
define('MD2_START_VOTE_MAIL_TEMPLATE', "start_vote_mail_template.txt");
define('MD2_SEND_INVITE_MAIL_TEMPLATE', "send_invite_mail_template.txt");
define('MD2_MAIL_TEMPLATE_SUBFOLDER', 'mail_templates');
define('MD2_MAIL_DATE_FORMAT', "l, F jS");

function md2_send_cron_related_mails($subject, $mailtext, $attachments = array())
{
  //$drs = md2_get_active_doctor_list();
  $drs = array();
  $drs[]="md2@kimandtodd.com";
  foreach ($drs as $doc)
  {
    send_digest_mail($doc, $subject, $mailtext, $attachments);
  }
}

function md2_send_vote_start_mail($dr)
{
  // Get template file path
  $path = trailingslashit(md2_get_plugin_pathdir()).trailingslashit(MD2_MAIL_TEMPLATE_SUBFOLDER).MD2_START_VOTE_MAIL_TEMPLATE;
  $tokens = array("__VOTE_END__","__MEET_DATE__");
  $vote_end = date_create($dr->date_voting_ended, md2_get_default_tz());
  $date_of_meet = date_create($dr->date_of_meet,  md2_get_default_tz());
  $vals = array($vote_end->format(MD2_MAIL_DATE_FORMAT), $date_of_meet->format(MD2_MAIL_DATE_FORMAT));
  $msg = do_replace($path, $vals, $tokens); 
  md2_send_cron_related_mails("Grand Rounds Voting", $msg);
}

function md2_send_meeting_mail($dr)
{
  // Use do_replace func from mailer to populate the meeting mail template
  // Test the inclusion of attachments
  // Modify the md2_mailer func to accept them
  $path = trailingslashit(md2_get_plugin_pathdir()).trailingslashit(MD2_MAIL_TEMPLATE_SUBFOLDER).MD2_SEND_INVITE_MAIL_TEMPLATE;
  $tokens = array("__MEET_DATE__", "__MEET_TIME__");
  $date_of_meet = date_create($dr->date_of_meet, md2_get_default_tz());
  $time_of_meet = formatMySqlTime($dr->time_meet_start);
  $vals = array($date_of_meet->format(MD2_MAIL_DATE_FORMAT), $time_of_meet);
  $msg = do_replace($path, $vals, $tokens); 
  //$icspath = md2_get_ics_file($dr);
  md2_send_cron_related_mails("Grand Rounds Agenda: " . $date_of_meet->format('F, Y'), $msg);//, array($icspath));
}

// This is everyone from the doc list on the admin side who is marked active. 
function md2_get_active_doctor_list()
{
  global $wpdb;
  // Get the name and email addresses of all active doctors
  $sql = "SELECT `docEmail` FROM `tblDoctors` WHERE `docStatus`=1 AND `docType`=9";
  return $wpdb->get_col($sql);  
}

// Ask for the file. If it exists already,
// go ahead and use it. Set $forcecreate to true
// to recreate the file even if it exists. 
function md2_get_ics_file($dr, $forcecreate = false)
{
  $name = md2_get_ics_file_name($dr);
  $dir = md2_get_upload_base_dir();
  $fullfilepath = $dir.trailingslashit("ics").$name;
  
  if ($forcecreate && file_exists($fullfilepath))
  {
    md2_delete_ics_file($dr);
  }
  if (!file_exists($fullfilepath))
  {
    md2_create_ics_file($fullfilepath, $dr);
  }
  if (file_exists($fullfilepath))
  {
    return $fullfilepath;
  }
  else 
  {
    return null;
  }
}

// Write out the file. 
function md2_create_ics_file($fullfilepath, $dr)
{
  $rsrc = fopen($fullfilepath,'w');
  fwrite($rsrc, md2_get_ics_data($dr));
  fclose($rsrc); 
}

// Use as a possible way to delete older files. 
// Maybe loop through and delete anything
// with last year in the title. 
function md2_delete_ics_file($dr)
{
  $name = md2_get_ics_file_name($dr);
  $dir = md2_get_upload_base_dir();
  $fullfilepath = $dir.trailingslashit("ics").$name;
  @unlink($fullfilepath);
}

// Figure out where we are on the server
function md2_get_upload_base_dir()
{
  $pathinfo = wp_upload_dir();
  return trailingslashit($pathinfo['basedir']);
}

// Figure out where we are on the server
function md2_get_plugin_pathdir()
{
  $pathinfo = pathinfo(__FILE__);  
  return trailingslashit($pathinfo['dirname']);
}

// We need a consistent way to name the file(s) 
// using the month and year of the meeting. 
function md2_get_ics_file_name($dr)
{
  $fsrc = "grand_rounds_";
  $fsuf = ".ics";
  $tmp_date = date_create($dr->date_of_meet,md2_get_default_tz());
  $fdate=strtolower($tmp_date->format("M_Y"));
  
  return $fsrc.$fdate.$fsuf;  
}

function formatMySqlTime($t)
{
    if ($t=="")return "";
    $t_arr = explode(":",$t);
    $hrs = intval($t_arr[0]);
    $pm = $hrs>11?" p.m.":" a.m.";
    $h = $hrs>12?$hrs-12:($hrs===0?"12":$hrs);
    return $h.":".$t_arr[1].$pm;
}