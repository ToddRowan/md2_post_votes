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
define("VOTECOMMENTSDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_comments");
define("VOTESUGGESTIONSDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_suggestions");
define("SELECTEDPOSTSDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_winners");

require ("model/md2_date_range_model.php");

function md2_get_posts_by_post_date_range($date_range_id)
{
    global $wpdb;
    $range = md2_get_vote_date_range_by_id($id);
    // Need query that grabs by comment as well as post id. 
    $sql = "SELECT * FROM " . $wpdb->posts. " WHERE `post_type`='post' AND `post_status`='publish' ";
    $sql .= "AND `post_date` BETWEEN '". $range->start_date ."' AND '". $range->end_date ."' ORDER BY `post_date` DESC";
    
    $posts = $wpdb->get_results($sql);
    
    return $posts;
}

function md2_get_posts_by_comment_date_range($date_range_id)
{
    global $wpdb;
    $range = md2_get_vote_date_range_by_id($id);
    
    $sql =  "SELECT DISTINCT wp_posts.*, wpc.comment_date";
    $sql .= "FROM wp_md2_posts wp_posts ";
    $sql .= "JOIN wp_md2_comments wpc ON wpc.comment_post_id = wp_posts.id ";
    $sql .= "WHERE post_type = 'post' AND post_status = 'publish' ";
    $sql .= "AND (wp_posts.post_date < '". $range->start_date ."' AND wpc.comment_date BETWEEN '". $range->start_date ."' AND '". $range->end_date ."') ";
    $sql .= "GROUP BY wp_posts.ID ";
    $sql .= "ORDER BY wpc.comment_date DESC";
    
    $posts = $wpdb->get_results($sql);
    
    return $posts;
}

function md2_get_votes_by_date_range($date_range)
{
    
}

function md2_set_vote($post_id, $user_id, $date_range)
{
    global $wpdb;
    if (!did_user_vote_for_post($post_id, $user_id, $date_range))
        $wpdb->insert(VOTESDBTABLE, array("post_id"=>$post_id, "user_id"=>$user_id, "vote_daterange_id"=>$date_range)
            ,array("%d","%d","%d"));
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
    $sql = "SELECT `post_id`, COUNT(`user_id`) as votecount FROM " . VOTESDBTABLE . " WHERE `vote_daterange_id` = $date_range_id GROUP BY `post_id` ORDER BY votecount DESC";
    return $wpdb->query($sql);
}

function md2_get_votes_for_user($user_id, $date_range_id)
{
    global $wpdb;
    $sql="SELECT `post_id` FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id AND `vote_daterange_id` = $date_range_id";
    return $wpdb->get_col($sql);   
}
function md2_delete_vote($post_id, $user_id, $date_range_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id AND `post_id` = $post_id AND `vote_daterange_id` = $date_range_id";
    $wpdb->query($sql);
}

function md2_delete_votes_by_user($user_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `user_id` = $user_id";
    $wpdb->query($sql); 
}

function md2_delete_votes_by_post($post_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . VOTESDBTABLE . " WHERE `post_id` = $post_id";
    $wpdb->query($sql);
}