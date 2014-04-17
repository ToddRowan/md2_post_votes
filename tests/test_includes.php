<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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