<?php

/*date ranges (what about a title or description?)
CREATE TABLE `wp_md2_vote_dateranges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`)
) */

define("TIMEFRAMEDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_dateranges");
define("MD2_STATE_NOT_USED", 1*256); // Created but not used.
define("MD2_STATE_ACTIVATED", 2*256); //Activated, waiting to send vote start email. 
define("MD2_STATE_VOTE_MAIL_SENT", 3*256);  //Activated, vote email sent, voting open.
define("MD2_STATE_VOTE_COMPLETED", 4*256);  //Activated, voting over, waiting to send meeting email
define("MD2_STATE_MEET_MAIL_SENT", 5*256);  //Activated, voting over, meeting email and invite sent
define("MD2_STATE_ARCHIVED", 6*256);  //Archived, meeting date passed. 

function md2_create_vote_date_range($start, $end)
{
    global $wpdb;
    $d_start = make_mysql_date(date_parse($start));
    $d_end = make_mysql_date(date_parse($end));
    
    if ($wpdb->insert(TIMEFRAMEDBTABLE, array("start_date"=>$d_start, "end_date"=>$d_end),array("%s","%s")) == 1)
        return $wpdb->insert_id;
    else
        return false;
}

function md2_update_vote_date_range($id, $values)
{
    global $wpdb;
    
    $formats = array(); 
    
    $dates = array('start_date', 'end_date');
    $datetimes = array('date_of_meet','date_meet_email_sent','date_vote_email_sent', 
        'date_voting_ended');
    $times = array('time_meet_start', 'time_meet_end');
    
    /*if (isset($values['start_date']))
    {
        $values['start_date'] = make_mysql_date(date_parse($values['start_date']));
    }
    
    if (isset($values['end_date']))
    {
        $values['end_date'] = make_mysql_date(date_parse($values['end_date']));
    }*/
    
    foreach ($values as $k=>$v)
    {
      if ($k=='process_state')
      {
        $formats[]='%d';
      }
      else
      {
        $formats[]='%s';
      }
      
      if (in_array($k, $dates))
      {
        $values[$k] = make_mysql_date(date_parse($values[$k]));
      }
      
      if (in_array($k, $datetimes))
      {
        $values[$k] = make_mysql_datetime(date_parse($values[$k]));
      }
      
      if (in_array($k, $times))
      {
        $values[$k] = make_mysql_time($values[$k]);
      }
    }
    
    $result = $wpdb->update( TIMEFRAMEDBTABLE, $values, array('id'=> $id), (count($formats)>0?$formats:null), array('%d'));
    
    if ( false === $result) 
    {
      return new WP_Error( 'date_range_update_error', 
        'Update failure', $wpdb->last_error );
    } 
    else 
    {
      return $result;
    }
}

function md2_get_vote_date_range_by_id($id)
{
    global $wpdb;
    $sql = "SELECT * FROM " . TIMEFRAMEDBTABLE . " WHERE `id`=$id";
    return $wpdb->get_row($sql);
}

function md2_get_all_date_ranges()
{
    global $wpdb;
    $sql = "SELECT * FROM " . TIMEFRAMEDBTABLE . " ORDER BY `start_date` DESC";
    return $wpdb->get_results($sql);
}

function md2_is_date_range_activatable($id)
{
    $activatable = false;
    $now = time();
    /*
      Has the range been processed already (is locked)?
      Are any of the dates in the future?
      Are any other ranges currently active?
     */
    
    $range = md2_get_vote_date_range_by_id($id);
    
    if (intval($range->process_state)===MD2_STATE_NOT_USED 
            && count(md2_get_active_date_ranges($id)) == 0
            && $now > strtotime( $range->end_date ))
        $activatable = true;
     
    return $activatable;
}

/*
 * Need a definitive definition of what consitutes active and thus
 * precludes any other range from being activated. Actively. 
 */
function md2_get_active_date_ranges($exclude_id = -1)
{
    global $wpdb;
    $sql = "SELECT * FROM " . TIMEFRAMEDBTABLE . " WHERE";
    $sql .= ($exclude_id != -1 ? " NOT `id`=$exclude_id AND": "");
    $sql .= " `process_state`>=" . MD2_STATE_ACTIVATED . " AND ";
    $sql .= " `process_state`<=" . MD2_STATE_MEET_MAIL_SENT;
    $sql .= " ORDER BY `start_date` DESC";
    return $wpdb->get_results($sql);
}

function md2_get_voting_date_range()
{
    global $wpdb;
    $sql = "SELECT * FROM " . TIMEFRAMEDBTABLE . " WHERE";
    $sql .= " `is_voting_eligible`= 'y'";
    return $wpdb->get_results($sql);
}

function md2_get_youngest_archived_date_range()
{
  global $wpdb;
  $sql = "SELECT * FROM " . TIMEFRAMEDBTABLE . " WHERE `process_state` = " . MD2_STATE_ARCHIVED . " ORDER BY `end_date` DESC LIMIT 0,1";
  return $wpdb->get_row($sql);
}

function md2_get_all_archived_date_range_ids()
{
  global $wpdb;
  $sql = "SELECT ID FROM " . TIMEFRAMEDBTABLE . " WHERE `process_state` = " . MD2_STATE_ARCHIVED . " ORDER BY `end_date` ASC";
  return $wpdb->get_col($sql);
}

function md2_get_latest_start_date()
{
    global $wpdb;
    $sql = "SELECT `start_date` FROM " . TIMEFRAMEDBTABLE . " ORDER BY `start_date` DESC LIMIT 0,1";
    return date( 'n/j/Y', strtotime( $wpdb->get_var( $sql ) ) );
}

function md2_get_latest_end_date()
{
    global $wpdb;
    $sql = "SELECT `end_date` FROM " . TIMEFRAMEDBTABLE . " ORDER BY `end_date` DESC LIMIT 0,1";
    return date( 'n/j/Y', strtotime( $wpdb->get_var( $sql ) ) );
}

function get_latest_end_date_plus_one()
{
    global $wpdb;
    $sql = "SELECT `end_date` FROM " . TIMEFRAMEDBTABLE . " ORDER BY `end_date` DESC LIMIT 0,1";
    return date( 'n/j/Y', strtotime( $wpdb->get_var( $sql ) . " + 1 day") );
}

function make_mysql_date($in)
{
        $s = "-";
        return $in['year'] . $s . sprintf('%02d', $in['month']) . $s . sprintf('%02d', $in['day']);
}

function make_mysql_datetime($in, $time="00:00:00")
{
        return make_mysql_date($in) . " " . $time;
}

function make_mysql_time($time)
{
  $h = "";
  $s = "00";
  $bits = explode(':', $time);
  $hn = intval($bits[0]);
  $mn = intval(substr($bits[1],0,2));
  $ampm = substr($bits[1],2,2);
  if ($ampm=="pm")
  {
    if ($hn<12)
    {
      $h=sprintf('%02d', $hn+12);
    }
    else
    {
      $h = "12";
    }
  }
  else if ($hn==12)
  {
    $h = "00";
  }
  else 
  {
    $h=sprintf('%02d', $hn); 
  }
  return $h.":".sprintf('%02d', $mn).":".$s;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function is_date_earlier($d1, $d2)
{
    if ($d1['year']>$d2['year']) return false;
    if ($d1['year']==$d2['year'] && $d1['month'] > $d2['month']) return false;
    if ($d1['year']==$d2['year'] && $d1['month']==$d2['month'] && $d1['day'] >= $d2['day']) return false;
    return true;
}

function md2_is_voting_open()
{
  global $wpdb;
  
  $sql = "SELECT COUNT(`is_voting_eligible`) as tot FROM " . TIMEFRAMEDBTABLE;
  $sql.= " WHERE `is_voting_eligible`='y'";
  
  return ($wpdb->get_var($sql) > 0);
}