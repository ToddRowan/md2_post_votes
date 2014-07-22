<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function clear_eligible_posts_table()
{
    global $wpdb;
    $sql = "DELETE FROM ". ELIGIBLEPOSTSDBTABLE;
    $wpdb->query($sql);    
}

function count_eligible_posts()
{
    global $wpdb;
    $sql = "SELECT COUNT(*) FROM " . ELIGIBLEPOSTSDBTABLE;
    return $wpdb->get_var($sql);
}

function clear_vote_suggestion_table()
{
    global $wpdb;
    $sql = "DELETE FROM ". VOTESUGGESTIONSDBTABLE;
    $wpdb->query($sql);    
}

function count_suggestions()
{
    global $wpdb;
    $sql = "SELECT COUNT(*) FROM " . VOTESUGGESTIONSDBTABLE;
    return $wpdb->get_var($sql);
}

function clear_vote_comment_table()
{
    global $wpdb;
    $sql = "DELETE FROM ". VOTECOMMENTSDBTABLE;
    $wpdb->query($sql);    
}

function count_comments()
{
    global $wpdb;
    $sql = "SELECT COUNT(*) FROM " . VOTECOMMENTSDBTABLE;
    return $wpdb->get_var($sql);
}

function clear_date_ranges_table()
{
    global $wpdb;
    $sql = "DELETE FROM ". TIMEFRAMEDBTABLE;
    $wpdb->query($sql);    
}

function clear_votes_table()
{
    global $wpdb;
    $sql = "DELETE FROM ". VOTESDBTABLE;
    $wpdb->query($sql);    
}

function clear_date_range_votes_table()
{
  global $wpdb;
    $sql = "DELETE FROM ". DATERANGEVOTESDBTABLE;
    $wpdb->query($sql);  
}

function count_votes()
{
    global $wpdb;
    $sql = "SELECT COUNT(*) FROM " . VOTESDBTABLE;
    return $wpdb->get_var($sql);
}

function count_date_ranges()
{
    global $wpdb;
    $sql = "SELECT COUNT(*) FROM " . TIMEFRAMEDBTABLE;
    return $wpdb->get_var($sql);
}

function count_date_range_votes()
{
    global $wpdb;
    $sql = "SELECT COUNT(*) FROM " . DATERANGEVOTESDBTABLE;
    return $wpdb->get_var($sql);
}