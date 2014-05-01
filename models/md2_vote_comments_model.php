<?php

/*
  CREATE TABLE `wp_md2_vote_comments` (
  `post_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `date_range_id` bigint(20) NOT NULL,
  `comment_text` mediumtext CHARACTER SET latin1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

define("VOTECOMMENTSDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_comments");

function md2_create_vote_comment($user_id, $post_id, $date_range_id, $comment_text='')
{
    global $wpdb;
    
    if (!is_null(md2_get_vote_comment($user_id, $post_id, $date_range_id)))
    {
        return md2_update_vote_comment($user_id, $post_id, $date_range_id, $comment_text);
    }
    
    if ($wpdb->insert(VOTECOMMENTSDBTABLE, 
            array("user_id"=>$user_id, "post_id"=>$post_id, 
                  "date_range_id"=>$date_range_id, "comment_text"=>$comment_text), 
            array("%d","%d","%d","%s")) == 1)
        return true;
    else
        return false;
}

function md2_update_vote_comment($user_id, $post_id, $date_range_id, $comment_text='')
{
    global $wpdb;
    
    return $wpdb->update(VOTECOMMENTSDBTABLE, 
            array("comment_text"=>$comment_text), 
            array("user_id"=>$user_id, "post_id"=>$post_id, 
                  "date_range_id"=>$date_range_id),
            array("%s"), 
            array("%d","%d","%d"));
}

function md2_get_vote_comment($user_id, $post_id, $date_range_id)
{
    global $wpdb;
    $sql = "SELECT `comment_text` FROM " . VOTECOMMENTSDBTABLE . 
           " WHERE `user_id`=$user_id AND `post_id`=$post_id AND `date_range_id`=$date_range_id";
    
    return $wpdb->get_var($sql);
}

function md2_get_vote_comments_by_date_range($date_range_id)
{
    global $wpdb;
    $sql = "SELECT * FROM " . VOTECOMMENTSDBTABLE . 
           " WHERE `date_range_id`=$date_range_id";
    
    return $wpdb->get_results($sql);
}

function md2_delete_vote_comments_by_date_range($date_range_id)
{
    global $wpdb;
    
    $sql = "DELETE FROM " . VOTECOMMENTSDBTABLE . " WHERE `date_range_id`=$date_range_id";
    return $wpdb->query($sql);
}

function md2_delete_vote_comment($user_id, $post_id, $date_range_id)
{
    global $wpdb;
    
    $sql = "DELETE FROM " . VOTECOMMENTSDBTABLE . 
           " WHERE `user_id`=$user_id AND `post_id`=$post_id AND `date_range_id`=$date_range_id";
    return $wpdb->query($sql);
}

function md2_delete_vote_comments_by_user_and_daterange($user_id, $date_range_id)
{
    global $wpdb;
    
    $sql = "DELETE FROM " . VOTECOMMENTSDBTABLE . 
           " WHERE `user_id`=$user_id AND `date_range_id`=$date_range_id";
    return $wpdb->query($sql);
}

function md2_delete_vote_comments_by_user($user_id)
{
    global $wpdb;
    
    $sql = "DELETE FROM " . VOTECOMMENTSDBTABLE . " WHERE `user_id`=$user_id";
    return $wpdb->query($sql);
}

function md2_delete_vote_comments_by_post($post_id)
{
    global $wpdb;
    
    $sql = "DELETE FROM " . VOTECOMMENTSDBTABLE . " WHERE `post_id`=$post_id";
    return $wpdb->query($sql);
}