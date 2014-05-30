<?php

/**
 * @package md2_post_votes
 * @version 0.1
 */
/*
Plugin Name: MD2 votes on posts
Plugin URI: http://www.md2.com
Description: Enables MD2 users to vote on posts to choose items for a later discussion.
Author: Todd Rowan
Version: 0.1
*/

require ("md2_post_votes_model.php");
require ("md2_post_votes_admin.php");
require ("md2_post_votes_ajax.php");
require ("md2_post_votes_cron.php");


function did_user_vote_for_post($post_id = -1, $user_id=-1, $vote_period=-1)
{
    $vals = get_pid_and_uid_from_loop();
    if ($post_id != -1) $vals['pid']=$post_id;
    if ($user_id!=-1) $vals['uid']=$user_id;
    if ($vals['pid']==-1 || $vals['uid']==-1)
    {
        return false;
    }
    else
    {
        $rs = md2_get_vote($vals['pid'], $vals['uid'], $vote_period);
        if (is_null($rs))
            return false;
        else
            return true;            
    }
}

function get_vote_count_for_user($user_id, $date_range_id)
{
    return count(md2_get_votes_for_user($user_id, $date_range_id));
}

function get_vote_count_for_post($post_id, $date_range_id)
{
    return count(md2_get_votes_for_post($post_id, $date_range_id));
}

function get_posts_with_votes($date_range_id)
{
    
}

function md2_get_process_vote_action_url()
{
    return  plugins_url('md2_process_votes.php', __FILE__) ; 
}

function md2_orderby_modification($orderby, $wpquery)
{
    // Necessary b/c we're on WP 3.3.2 and not 3.5
    global $wpdb;
    if ($wpquery->query_vars['orderby'] === 'post__in' 
            && is_array($wpquery->query_vars['post__in'])
            && count($wpquery->query_vars['post__in'])>0)
    {
        return "FIELD( {$wpdb->posts}.ID, " . implode(",", $wpquery->query_vars['post__in']). " )";
    }
    else 
    {
        return $orderby;
    }
}

add_filter('posts_orderby', 'md2_orderby_modification',2,2);