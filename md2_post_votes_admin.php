<?php

/* 
 * All the bits for our administration pages.
 */

function add_md2_vote_options()
{
    add_options_page( "Voting configuration", 
                      'Voting configuration', 
                      'activate_plugins', 
                      'vote_options', 'md2_generate_vote_options');
}

function md2_generate_vote_options()
{
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>
        <h2>Voting configuration</h2>
        <p id="newVoteRange">
            <input type="text" class="date start" /> to
            <input type="text" class="date end" />
        </p>
    </div> <!-- wrap -->
    
    <?php
}

function add_md2_calendar_scripts()
{
    //wp_register_script('md2jq', 'http://www.md2.com/portal/wp-includes/js/jquery/jquery.js?ver=1.7.1');
    wp_register_style('bootstrap-datepicker', plugins_url('calendar/lib/bootstrap-datepicker.css', __FILE__));
    wp_register_script('bootstrap-datepicker', plugins_url('calendar/lib/bootstrap-datepicker.js', __FILE__),array('jquery'));
    wp_register_script('datepair', plugins_url('calendar/jquery.datepair.min.js', __FILE__), array('bootstrap-datepicker'));
    wp_register_script('md2_vote_tools_init', plugins_url('calendar/voting_tools_init.js', __FILE__), array('datepair'));
    wp_enqueue_style('bootstrap-datepicker');
    wp_enqueue_script('md2_vote_tools_init');
}


add_action('admin_menu', 'add_md2_vote_options');
add_action('admin_enqueue_scripts', 'add_md2_calendar_scripts');