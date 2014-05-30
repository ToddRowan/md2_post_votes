<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
  <head>
    <meta charset="UTF-8">
    <title></title>
  </head>
  <body>
    <h1>Cron tests</h1>
    <?php
    require("../../../../wp-load.php");
    require("test_includes.php");
    // Clear cron tables?
    
    // Create a date range
    $start = "April 1, 2014";
    $end = "April 15, 2014";
    
    $id = md2_create_vote_date_range($start, $end);     
    
    // activate it
    /*Set these fields */
    $_POST['id'] = $id;
    $_POST['act']='activate';
    $_POST['date_of_meet']= "May 1, 2014";
    $_POST['time_meet_start']="9:30am";
    $_POST['time_meet_end']="10:30am";
    $_POST['phone_number']="888-555-1212";
    $_POST['meeting_id']="123456";
    $_POST['meeting_note']="Note,note,note,note";
    $_POST['date_meet_email_sent']= "April 21, 2014";
    $_POST['date_vote_email_sent']= "April 17, 2014";
    $_POST['date_voting_ended']= "April 19, 2014";
    
     
    md2_activate_date_range_ajax();
    
    
    // run it through each cron state
    for ($x=0;$x<6;$x++)
    {
      md2_do_vote_cron($id);
    }    
    
    // delete the date range
    
    ?>
  </body>
</html>
