<?php

define("TIMEFRAMEDBTABLE", $wpdb->prefix . ($wpdb->prefix==='wp_md2_'?'':'md2_') . "vote_dateranges");

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

function md2_get_vote_date_range_by_id($id)
{
    global $wpdb;
    $sql = "SELECT * FROM " . TIMEFRAMEDBTABLE . " WHERE `id`=$id";
    return $wpdb->get_row($sql);
}

function md2_get_all_date_ranges()
{
    global $wpdb;
    $sql = "SELECT * FROM " . TIMEFRAMEDBTABLE . " ORDER BY `start_date` DESC";
    return $wpdb->get_results($sql);
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

function make_mysql_date($in)
{
        $s = "-";
        return $in['year'] . $s . sprintf('%02d', $in['month']) . $s . sprintf('%02d', $in['day']);
}

function is_date_earlier($d1, $d2)
{
    if ($d1['year']>$d2['year']) return false;
    if ($d1['year']==$d2['year'] && $d1['month'] > $d2['month']) return false;
    if ($d1['year']==$d2['year'] && $d1['month']==$d2['month'] && $d1['day'] >= $d2['day']) return false;
    return true;
}