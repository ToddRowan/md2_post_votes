<?php

/* 
 * All the bits for our administration pages.
 */

define("VOTE_OPTIONS_TAG", 'vote_options');

function md2_add_vote_options()
{
    add_options_page( "Voting configuration", 
                      'Voting configuration', 
                      'activate_plugins', 
                      VOTE_OPTIONS_TAG, 'md2_generate_vote_options');
}

function md2_generate_vote_options()
{
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2>Voting configuration</h2>
        <?php 
            $res = md2_get_all_date_ranges()        
        ?>
        <p>Here are the <?php echo count($res); ?> saved date ranges that exist in the system.</p>
        
        <?php
            echo "<table id=\"date_range_table\"><th>Start</th><th>End</th><th>Active</th><th>Votes</th><th>Edit</th>";
            foreach ($res as $date_range)
            {                
                echo "<tr><td>".date( 'M j, Y', strtotime($date_range->start_date))."</td><td>".date( 'M j, Y', strtotime($date_range->end_date))."</td>";
                echo "<td>" . ($date_range->is_voting_eligible==='0'?'No':'Yes') . "</td><td></td><td></td></tr>";             
            }
            echo "</table>";               
        ?>
       
        <form action="<?php echo plugins_url('md2_vote_options_post.php', __FILE__);?>" method="post">
            <p id="newVoteRange">
                <input id="md2_date_range_start" name="md2_date_range_start" type="text" class="date start" /> <br>to<br>
                <input id="md2_date_range_end" name="md2_date_range_end" type="text" class="date end" />
            </p>
            <button type="submit">Add a new date range</button>
        </form>
    </div> <!-- div.wrap -->
    
    <?php
}

function md2_add_vote_calendar_scripts()
{
    //wp_register_script('md2jq', 'http://www.md2.com/portal/wp-includes/js/jquery/jquery.js?ver=1.7.1');
    wp_register_style('zebra-datepicker', plugins_url('calendar/css/default.css', __FILE__));
    wp_register_style('md2-votes-admin-style', plugins_url('css/votes_admin.css', __FILE__));
    wp_register_script('zebra-datepicker-js', plugins_url('calendar/javascript/zebra_datepicker.js', __FILE__), array('jquery'));
    wp_register_script('md2_vote_tools_init', plugins_url('calendar/voting_tools_init.js', __FILE__), array('zebra-datepicker-js'));
    wp_enqueue_style('zebra-datepicker');
    wp_enqueue_style('md2-votes-admin-style');
    wp_enqueue_script('md2_vote_tools_init');
}


add_action('admin_menu', 'md2_add_vote_options');
add_action('admin_enqueue_scripts', 'md2_add_vote_calendar_scripts');