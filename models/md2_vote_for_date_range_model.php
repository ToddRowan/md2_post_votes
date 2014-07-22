<?php

/* 
CREATE TABLE `wp_md2_vote_daterangevotes` (
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date_range_id` bigint(20) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

define("DATERANGEVOTESDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_daterangevotes");

function md2_get_date_range_votes_by_date_range($date_range_id)
{
  global $wpdb;
  
  $sql = "SELECT `u`.`user_login` ";
  $sql.= "FROM " . $wpdb->users . " `u` LEFT OUTER JOIN ". DATERANGEVOTESDBTABLE ." `v` ";
  $sql.= "ON `u`.`ID` = `v`.`user_id` ";
  $sql.= "WHERE `v`.`date_range_id` = $date_range_id ";
  $sql.= "ORDER BY `u`.`user_login` ASC";
  
  return $wpdb->get_col($sql);
}

function md2_add_date_range_vote($date_range_id, $user_id)
{
  global $wpdb;
  
  if (md2_did_user_vote_for_date_range($date_range_id, $user_id))
      return true;
    
  if ($wpdb->insert(DATERANGEVOTESDBTABLE, array("user_id"=>$user_id, "date_range_id"=>$date_range_id),
                   array("%d","%d")) == 1)
        return true;
    else
        return false;
}

function md2_delete_date_range_vote($date_range_id, $user_id)
{
  global $wpdb;
  $sql = "DELETE FROM " . DATERANGEVOTESDBTABLE . " WHERE `user_id` = $user_id AND ";
  $sql.= "`date_range_id` = $date_range_id";
  $wpdb->query($sql);
}

function md2_delete_date_range_votes_by_date_range($date_range_id)
{
  global $wpdb;
  $sql = "DELETE FROM " . DATERANGEVOTESDBTABLE . " WHERE `date_range_id` = $date_range_id";
  $wpdb->query($sql);
}

function md2_delete_date_range_votes_by_user($user_id)
{
  global $wpdb;
  $sql = "DELETE FROM " . DATERANGEVOTESDBTABLE . " WHERE `user_id` = $user_id";
  $wpdb->query($sql);
}

function md2_did_user_vote_for_date_range($date_range_id, $user_id)
{
  global $wpdb;
  $sql = "SELECT * FROM " . DATERANGEVOTESDBTABLE . " WHERE `user_id` = $user_id AND `date_range_id` = $date_range_id";
  return count($wpdb->get_results($sql)) > 0;
}

function md2_get_vote_count_for_date_range($date_range_id)
{
  global $wpdb;
  $sql = "SELECT * FROM " . DATERANGEVOTESDBTABLE . " WHERE `date_range_id` = $date_range_id";
  return count($wpdb->get_results($sql)); 
}