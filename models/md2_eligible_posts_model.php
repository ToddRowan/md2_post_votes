<?php

/*
  CREATE TABLE `wp_md2_vote_eligible_posts` (
  `post_id` bigint(20) NOT NULL,
  `date_range_id` bigint(20) NOT NULL,
  `sort_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 */

define("ELIGIBLEPOSTSDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_eligible_posts");

function md2_get_eligible_posts_by_date_range($date_range_id)
{
    global $wpdb;
    $sql = "SELECT * FROM " . ELIGIBLEPOSTSDBTABLE . " WHERE `date_range_id` = " . $date_range_id;
    return $wpdb->get_results($sql);
}

function md2_get_eligible_post_by_id_and_date_range($post_id, $date_range_id)
{
    global $wpdb;
    $sql = "SELECT * FROM " . ELIGIBLEPOSTSDBTABLE . " WHERE `post_id` = " . $post_id . " AND `date_range_id` = " . $date_range_id;
    return $wpdb->get_results($sql);
}

function md2_add_eligible_post($post_id, $date_range_id, $sort_date)
{
    global $wpdb;
    
    if (md2_is_post_eligible_for_date_range($post_id, $date_range_id)) return true;
    
    if ($wpdb->insert(ELIGIBLEPOSTSDBTABLE, array("post_id"=>$post_id, "date_range_id"=>$date_range_id, "sort_date"=>$sort_date),
                    array("%d","%d","%s")) == 1)
        return true;
    else
        return false;
}

function md2_delete_eligible_post_by_id($post_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . ELIGIBLEPOSTSDBTABLE . " WHERE `post_id` = $post_id";
    $wpdb->query($sql);
}

function md2_delete_eligible_post_by_date_range($date_range_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . ELIGIBLEPOSTSDBTABLE . " WHERE `date_range_id` = $date_range_id";
    $wpdb->query($sql);
}

function md2_delete_eligible_post_by_id_and_date_range($post_id, $date_range_id)
{
    global $wpdb;
    $sql = "DELETE FROM " . ELIGIBLEPOSTSDBTABLE . " WHERE `post_id` = $post_id AND `date_range_id` = $date_range_id";
    $wpdb->query($sql);
}

function md2_is_post_eligible_for_date_range($post_id, $date_range_id)
{
    return count(md2_get_eligible_post_by_id_and_date_range($post_id, $date_range_id))>0;
}