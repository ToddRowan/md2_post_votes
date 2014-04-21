<?php
require ("../../../wp-load.php");

// Save shit, then redirect back to our main page. 
// Do something if user not logged in or lacks capability.

// Save new date thing:

if (isset($_POST['md2_date_range_start']) && isset($_POST["md2_date_range_end"]))
{
    md2_create_vote_date_range($_POST['md2_date_range_start'], $_POST['md2_date_range_end']);
}


header("Location:" . get_admin_url() ."options-general.php?page=".VOTE_OPTIONS_TAG);
die();