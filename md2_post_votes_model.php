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

define("VOTESDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "votes");
define("SELECTEDPOSTSDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_winners");

require ("models/md2_vote_date_range_model.php");
require ("models/md2_vote_comments_model.php");
require ("models/md2_vote_suggestions_model.php");
require ("models/md2_eligible_posts_model.php");

function md2_get_posts_by_post_date_range($date_range_id)
{
    global $wpdb;
    $range = md2_get_vote_date_range_by_id($date_range_id);

    $sql = "SELECT ID as post_id, post_date FROM " . $wpdb->posts. " WHERE `post_type`='post' AND `post_status`='publish' ";
    $sql .= "AND `post_date` BETWEEN '". $range->start_date ."' AND '". $range->end_date ."' ORDER BY `post_date` DESC";
    
    $posts = $wpdb->get_results($sql);
    
    return $posts;
}

function md2_get_posts_by_comment_date_range($date_range_id)
{
    global $wpdb;
    $range = md2_get_vote_date_range_by_id($date_range_id);
    
    $sql =  "SELECT DISTINCT wp_posts.id as post_id, {$wpdb->comments}.comment_date as post_date ";
    $sql .= "FROM {$wpdb->posts} wp_posts ";
    $sql .= "JOIN {$wpdb->comments} ON {$wpdb->comments}.comment_post_id = wp_posts.id ";
    $sql .= "WHERE post_type = 'post' AND post_status = 'publish' ";
    $sql .= "AND (wp_posts.post_date < '". $range->start_date ."' AND {$wpdb->comments}.comment_date BETWEEN '". $range->start_date ."' AND '". $range->end_date ."') ";
    $sql .= "GROUP BY wp_posts.ID ";
    $sql .= "ORDER BY {$wpdb->comments}.comment_date DESC";
    
    $posts = $wpdb->get_results($sql);
    
    return $posts;
}

function md2_get_count_of_posts_by_post_date_range($date_range_id)
{
    return count(md2_get_posts_by_post_date_range($date_range_id));
}

function md2_get_count_of_posts_by_comment_date_range($date_range_id)
{
    return count(md2_get_posts_by_comment_date_range($date_range_id));
}

function md2_get_total_count_of_posts_by_date_range($date_range_id)
{
    return md2_get_count_of_posts_by_post_date_range($date_range_id) + md2_get_count_of_posts_by_comment_date_range($date_range_id);
}

function md2_set_vote($post_id, $user_id, $date_range)
{
    global $wpdb;
    if (!did_user_vote_for_post($post_id, $user_id, $date_range))
    {
        $wpdb->insert(VOTESDBTABLE
            ,array("post_id"=>$post_id, "user_id"=>$user_id, "vote_daterange_id"=>$date_range)
            ,array("%d","%d","%d"));
    }
}

function md2_get_vote($post_id, $user_id, $date_range_id)
{
    global $wpdb;
    $sql="SELECT * FROM " . VOTESDBTABLE . " WHERE `post_id` = $post_id AND `user_id` = $user_id AND `vote_daterange_id` = $date_range_id";
    return $wpdb->get_row($sql);
}

function md2_get_votes_for_post($post_id, $date_range_id)
{
    global $wpdb;
    $sql="SELECT `user_id` FROM " . VOTESDBTABLE . " WHERE `post_id` = $post_id AND `vote_daterange_id`  = $date_range_id";
    return $wpdb->get_col($sql);   
}

function md2_get_posts_ranked_by_votes($date_range_id)
{
    global $wpdb;
    $sql = "SELECT `post_id`, COUNT(`user_id`) as votecount FROM " . VOTESDBTABLE ;
    $sql.= " WHERE `vote_daterange_id` = $date_range_id GROUP BY `post_id` ORDER BY votecount DESC";
    return $wpdb->query($sql);
}

function md2_get_post_vote_counts($date_range_id)
{
  global $wpdb;

  $sql="SELECT `p`.`ID`, `p`.`post_title`, COUNT(`v`.`post_id`) AS `votecount` FROM ";
  $sql.= $wpdb->posts . " p INNER JOIN ". VOTESDBTABLE . " `v` ON `p`.`ID` = `v`.`post_id` ";
  $sql.= " WHERE `v`.`vote_daterange_id` = $date_range_id GROUP BY `v`.`post_id` ";
  $sql.= " ORDER BY `votecount` DESC";
  
  return $wpdb->get_results($sql);
}

function md2_get_user_vote_counts($date_range_id)
{
  global $wpdb;
  $sql = "SELECT `u`.`ID`, `u`.`user_login`, COUNT(`v`.`user_id`) AS `votecount` ";
  $sql.= "FROM " . $wpdb->users . " `u` LEFT OUTER JOIN ". VOTESDBTABLE ." `v` ";
  $sql.= "ON `u`.`ID` = `v`.`user_id` ";
  $sql.= "WHERE `v`.`vote_daterange_id` = $date_range_id ";
  $sql.= "GROUP BY `v`.`user_id` ORDER BY `votecount` DESC, `u`.`user_login` ASC";
  
  return $wpdb->get_results($sql);
}

function md2_get_users_without_votes($date_range_id, $excludeids = array(1,2,9,12))
{
  global $wpdb;
  
  $sql = "SELECT `u`.`user_login` ";
  $sql.= "FROM {$wpdb->users} `u` ";
  $sql.= "LEFT JOIN (SELECT * FROM ". VOTESDBTABLE ." WHERE ". VOTESDBTABLE .".vote_daterange_id = ";
  $sql.= $date_range_id . ") v ON `v`.`user_id` = `u`.`ID` ";
  $sql.= "LEFT JOIN wp_md2_usermeta m ";
  $sql.= "ON u.ID=m.user_id ";
  $sql.= "WHERE m.meta_key='wp_md2_type' AND m.meta_value='9' ";
  $sql.= "AND v.user_id IS NULL ";
  $sql.= "AND NOT u.ID IN(". implode(",", $excludeids). ") " ;
  $sql.= "ORDER BY `u`.`user_login`";
    
  return $wpdb->get_results($sql);
}

function md2_get_votes_for_user($user_id, $date_range_id)
{
    global $wpdb;
    $sql = "SELECT `post_id` FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id";
    $sql.= " AND `vote_daterange_id` = $date_range_id";
    return $wpdb->get_col($sql);   
}

function md2_delete_vote($post_id, $user_id, $date_range_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id AND ";
    $sql.= "`post_id` = $post_id AND `vote_daterange_id` = $date_range_id";
    $wpdb->query($sql);
}

function md2_delete_votes_by_user($user_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id";
    $wpdb->query($sql); 
}

function md2_delete_votes_by_user_and_date_range($user_id, $date_range_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id AND `vote_daterange_id` = $date_range_id";
    $wpdb->query($sql); 
}

function md2_delete_votes_by_post($post_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `post_id` = $post_id";
    $wpdb->query($sql);
}

function md2_set_eligible_posts($date_range_id)
{
  global $wpdb;
  $sql = "SELECT DISTINCT `post_id` FROM " . VOTESDBTABLE . " WHERE `vote_daterange_id` = " . $date_range_id . " ORDER BY `post_id`";
  $ids =  $wpdb->get_col($sql);
  
  foreach ($ids as $id)
  {
    md2_set_eligible_post_selection_status($id, $date_range_id);
  }
}