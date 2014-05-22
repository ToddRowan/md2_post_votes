<?php

/* 
 * All the bits for our administration pages.
 */

define("VOTE_OPTIONS_TAG", 'vote_options');

function md2_add_vote_options()
{
    add_options_page( "Grand Rounds configuration", 
                      'Grand Rounds configuration', 
                      'activate_plugins', 
                      VOTE_OPTIONS_TAG, 'md2_generate_grand_rounds_admin');
}

function md2_generate_grand_rounds_admin()
{
    $res = md2_get_all_date_ranges();
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2>Grand Rounds configuration</h2>
        <p>Use this page to administrate the Grand Rounds voting periods and mass emailers.</p>
        <div id="gr_accordion">
            <h3>Post date ranges</h3>
            <div id="date_range_blade">
                <?php md2_date_range_config_blade($res); ?>
            </div>
            <h3>Important dates for this rounds period <span class="important_dates"></span></h3>
            <div id="vote_period_blade">
                <?php md2_vote_period_config_blade($res); ?>
            </div>
            <h3>Voting summary <span class="important_dates"></span></h3>
            <div id="vote_summary_blade">
                <?php md2_vote_results_blade($res); ?>
            </div>
        </div> <!-- accordion -->        
    </div> <!-- div.wrap -->    
    <?php
}

function md2_date_range_config_blade(&$res)
{
    ?>
    <p>Here are the <?php echo count($res); ?> saved date ranges that exist in the system. <span id="add_new_date_range">Add new.</span></p>
        
    <?php
        echo "<table id=\"date_range_table\"><tr><th>Select</th><th>Start</th><th>End</th><th>Voting status</th><th>Eligible posts</th><th>Activate</th><th>Edit dates</th></tr>";
        $selected = false;
        foreach ($res as $date_range)
        {                
            echo "<tr " . (!$selected?'class="selected_row" ':"") . "id=\"dr_row-" . $date_range->id . "\"><td class=\"center\">" . '<div class="select" id="select-'. $date_range->id . '"></div>' . "</td>";
            echo "<td><span id=\"start_date-". $date_range->id . '">' . date( 'M j, Y', strtotime($date_range->start_date))."</span></td>";
            echo "<td><span id=\"end_date-". $date_range->id . '">' . date( 'M j, Y', strtotime($date_range->end_date))."</span></td>";
            echo "<td>" . md2_output_voting_status($date_range) . "</td>";
            echo "<td>" . md2_get_total_count_of_posts_by_date_range($date_range->id) . "</td>";
            echo "<td>" . (md2_is_date_range_activatable($date_range->id)?'<span class="activate" id="activate-' . $date_range->id . '">Activate</span>':"") ."</td>";
            echo "<td>" . ($date_range->is_locked==='y'||$date_range->is_voting_eligible==='y'||$date_range->vote_mail_sent==='y'?'':'<span class="edit" id="edit-' . $date_range->id . '">Edit</span>') . "</td>";
            echo "<td class=\"data_column\"><div id=\"data-block-" . $date_range->id . "\">";
            echo "<span class=\"is_locked\">{$date_range->is_locked}</span>";
            echo "<span class=\"is_voting_eligible\">{$date_range->is_voting_eligible}</span>";
            echo "<span class=\"vote_mail_sent\">{$date_range->vote_mail_sent}</span>";
            echo "<span class=\"meeting_mail_sent\">{$date_range->meeting_mail_sent}</span>";
            echo "<span class=\"date_vote_email_sent\">" . md2_format_view_date($date_range->date_vote_email_sent). "</span>";
            echo "<span class=\"date_voting_ended\">" . md2_format_view_date($date_range->date_voting_ended) . "</span>";
            echo "<span class=\"date_meet_email_sent\">" . md2_format_view_date($date_range->date_meet_email_sent) . "</span>";
            echo "<span class=\"date_post_selection_ended\">" . md2_format_view_date($date_range->date_post_selection_ended) . "</span>";
            echo "<span class=\"date_of_meet\">" . md2_format_view_date($date_range->date_of_meet) . "</span>";
            echo "<span class=\"time_meet_start\">{$date_range->time_meet_start}</span>";
            echo "<span class=\"time_meet_end\">{$date_range->time_meet_end}</span>";
            echo "<span class=\"phone_number\">{$date_range->phone_number}</span>";
            echo "<span class=\"meeting_id\">{$date_range->meeting_id}</span>";
            echo "<span class=\"meeting_note\">{$date_range->meeting_note}</span>";
            echo "</div><!-- end data block --></td></tr>";
            $selected = true;
        }
        echo "</table>";               
    ?>

    <div id="new_date_range_form">
        <form action="<?php echo plugins_url('md2_vote_options_post.php', __FILE__);?>" method="post" id="md2_date_form">
            <p id="newVoteRange">
                <input id="md2_date_range_start" name="md2_date_range_start" type="text" class="date start" /> <br>to<br>
                <input id="md2_date_range_end" name="md2_date_range_end" type="text" class="date end" />
            </p>
            <button type="submit" id="date_range_submit">Add a new date range</button>&nbsp;<span id="date_range_edit_reset">Cancel</span>
        </form>
    </div>
    <?php
}

function md2_vote_period_config_blade(&$res)
{
    // On edit, set/edit cron job. 
    // UI pieces:
    // No dates
    // Editable dates
    //   Includes Save and activate.
    // Uneditable dates
    ?>
    <p class="voteopen">Voting for this period will begin on <input class="date date_vote_email_sent" name="date_vote_email_sent" type="text">
        and end on <input class="date date_voting_ended" name="date_voting_ended" type="text">.</p>
    <p class="voteclosed">Voting for this period was open from <span class="date_vote_email_sent"></span> 
        to <span class="date_voting_ended"></span>.</p>

    <p class="meetopen">The announcement email for rounds meeting will be sent on 
        <input class="date date_meet_email_sent" name="date_meet_email_sent" type="text">.</p>
    <p class="meetclosed">The announcement email for this period was sent on <span class="date_meet_email_sent"></span>.</p>
    
    <p class="meetopen">The meeting will be on <input class="date date_of_meet" name="date_of_meet" type="text">
        from <input class="time time_meet_start" name="time_meet_start" type="text"> to 
        <input class="time time_meet_end" name="time_meet_end" type="text"> (Pacific Time).</p>
    
    <p class="meetclosed">The meeting was scheduled for <span class="date_of_meet"></span>
        from <span class="time_meet_start"></span> to <span class="time_meet_end"></span>.</p>        
        
    <p class="meetopen">Callers will dial <input class="phone_number" name="phone_number" type="text"> and use the code <input class="meeting_id" name="meeting_id" type="text">.</p>
    <p class="meetclosed">Callers dialed <span class="phone_number"></span> and used the code <span class="meeting_id"></span>.</p>

    <p class="meetopen">Any additional info or special instructions? <input class="meeting_note" name="meeting_note" type="text"></p>
    <p class="meetclosed">Callers were instructed to also do the following: <span class="meeting_note"></span></p>

    
    <?php
}

function md2_vote_results_blade(&$res)
{
    ?>
    <p>This period has not been activated.</p>    
    <p>Voting is ACTIVE/INACTIVE.</p>
    <p>Of the NUM eligible posts, NUM have received votes. NUM doctors have voted during this period.</p>
    <p>OR</p>
    <p>Of the NUM eligible posts, NUM received votes. NUM doctors voted during this period.</p>
    <p>Here are the posts that HAVE received votes</p>
    <?php
    // Doc names?
    // Select posts for inclusion? Where do we do this?
}

function md2_add_grand_rounds_scripts()
{
    //wp_register_script('md2jq', 'http://www.md2.com/portal/wp-includes/js/jquery/jquery.js?ver=1.7.1');
    wp_register_style('jqui-style', plugins_url('jqui/css/flick/jquery-ui-1.10.4.custom.min.css', __FILE__));
    wp_register_style('jq_timepicker', plugins_url('jqui/css/jquery.timepicker.css', __FILE__));
    wp_register_style('md2-votes-admin-style', plugins_url('css/votes_admin.css', __FILE__), array('jqui-style','jq_timepicker'));
    wp_register_script('jqui', plugins_url('jqui/jquery-ui-1.10.4.custom.min.js', __FILE__), array('jquery'));
    wp_register_script('jq_time_picker', plugins_url('jqui/jquery.timepicker.min.js', __FILE__), array('jquery'));
    wp_register_script('md2_vote_tools_init', plugins_url('jqui/voting_tools_init.js', __FILE__), array('jqui','jq_time_picker'));

    wp_enqueue_style('md2-votes-admin-style');
    wp_enqueue_script('jq_time_picker');
    wp_enqueue_script('md2_vote_tools_init');
}

function md2_format_view_date($d)
{
    if (!is_null($d))
    {
        return date('M j, Y', strtotime($d));
    }
    else
    {
        return "";
    }
}

function md2_output_voting_status($dr)
{
    if ($dr->is_locked==='y')
        return 'Archived';
    if ($dr->is_voting_eligible==='y')
        return 'Voting';    
    if ($dr->vote_mail_sent==='n')
        return 'Not activated';
    
    return 'In progress'; 
}


add_action('admin_menu', 'md2_add_vote_options');
add_action('admin_enqueue_scripts', 'md2_add_grand_rounds_scripts');