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
    $sql = "SELECT * FROM " . ELIGIBLEPOSTSDBTABLE . " WHERE `date_range_id` = " . $date_range_id . " ORDER BY `sort_date`";
    return $wpdb->get_results($sql);
}

function md2_get_eligible_post_ids_by_date_range($date_range_id)
{
    global $wpdb;
    $sql = "SELECT `post_id` FROM " . ELIGIBLEPOSTSDBTABLE . " WHERE `date_range_id` = " . $date_range_id . " ORDER BY `sort_date`";
    return $wpdb->get_col($sql);
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

function md2_populate_eligible_posts_by_date_range($date_range_id)
{
    $el_posts_by_date = md2_get_posts_by_post_date_range($date_range_id);
    $el_posts_by_comment_date = md2_get_posts_by_comment_date_range($date_range_id);
    $all_posts = array_merge($el_posts_by_date, $el_posts_by_comment_date);
    foreach ($all_posts as $p)
    {
        md2_add_eligible_post($p->post_id, $date_range_id, $p->post_date);
    }
    
    return count($all_posts);
}