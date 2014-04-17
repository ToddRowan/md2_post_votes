<?php

/* DB tables 
date ranges (what about a title or description?)
CREATE TABLE `wp_md2_vote_dateranges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`)
)
  
 CREATE TABLE `wp_md2_votes` (
  `user_id` bigint(20) unsigned NOT NULL,
  `post_id` bigint(20) unsigned NOT NULL,
  `vote_daterange_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`post_id`,`vote_daterange_id`)
) 
  
 SELECT * FROM `calendar` WHERE DATE(startTime) = '2010-04-29'
user votes - post, date range, user
user comments on reason for vote - vote id
User additional suggestions - date range
Selected posts for discussion - date range
Date of post creation for voting and discussion (needs time limit for visibility) 
    - date range? or just custom fields on the posts?
  */

define("TIMEFRAMEDBTABLE", $wpdb->prefix . "md2_vote_dateranges");
define("VOTESDBTABLE", $wpdb->prefix . "md2_votes");
define("VOTECOMMENTSDBTABLE", $wpdb->prefix . "md2_vote_comments");
define("VOTESUGGESTIONSDBTABLE", $wpdb->prefix . "md2_vote_suggestions");
define("SELECTEDPOSTSDBTABLE", $wpdb->prefix . "md2_vote_winners");

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

function make_mysql_date($in)
{
        $s = "-";
        return $in['year'] . $s . sprintf('%02d', $in['month']) . $s . sprintf('%02d', $in['day']);
}

function get_latest_start_date()
{
    global $wpdb;
    $sql = "SELECT `start_date` FROM " . TIMEFRAMEDBTABLE . " ORDER BY `start_date` DESC LIMIT 0,1";
    return date( 'n/j/Y', strtotime( $wpdb->get_var( $sql ) ) );
}

function get_latest_end_date()
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

function is_date_earlier($d1, $d2)
{
    if ($d1['year']>$d2['year']) return false;
    if ($d1['year']==$d2['year'] && $d1['month'] > $d2['month']) return false;
    if ($d1['year']==$d2['year'] && $d1['month']==$d2['month'] && $d1['day'] >= $d2['day']) return false;
    return true;
}

function set_vote($post_id, $user_id, $date_range)
{
    global $wpdb;
    if (!did_user_vote_for_post($post_id, $user_id, $date_range))
        $wpdb->insert(VOTESDBTABLE, array("post_id"=>$post_id, "user_id"=>$user_id, "vote_daterange_id"=>$date_range)
            ,array("%d","%d","%d"));
}

function get_vote($post_id, $user_id, $date_range_id)
{
    global $wpdb;
    $sql="SELECT * FROM " . VOTESDBTABLE . " WHERE `post_id` = $post_id AND `user_id` = $user_id AND `vote_daterange_id` = $date_range_id";
    return $wpdb->get_row($sql);
}

function get_votes_for_post($post_id, $date_range_id)
{
    global $wpdb;
    $sql="SELECT `user_id` FROM " . VOTESDBTABLE . " WHERE `post_id` = $post_id AND `vote_daterange_id`  = $date_range_id";
    return $wpdb->get_col($sql);   
}

function get_posts_ranked_by_votes($date_range_id)
{
    global $wpdb;
    $sql = "SELECT `post_id`, COUNT(`user_id`) as votecount FROM " . VOTESDBTABLE . " WHERE `vote_daterange_id` = $date_range_id GROUP BY `post_id` ORDER BY votecount DESC";
    return $wpdb->query($sql);
}

function get_votes_for_user($user_id, $date_range_id)
{
    global $wpdb;
    $sql="SELECT `post_id` FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id AND `vote_daterange_id` = $date_range_id";
    return $wpdb->get_col($sql);   
}
function delete_vote($post_id, $user_id, $date_range_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id AND `post_id` = $post_id AND `vote_daterange_id` = $date_range_id";
    $wpdb->query($sql);
}

function delete_votes_by_user($user_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id";
    $wpdb->query($sql); 
}

function delete_votes_by_post($post_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `post_id` = $post_id";
    $wpdb->query($sql);
}