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
    <?php
    require("wp-load.php");
    $id = $_GET['id'];
    $dr = md2_get_vote_date_range_by_id($id);
    /// DO NOT USE UNLESS HACKED TO RETURN
    md2_send_vote_start_mail($dr);
    echo "<p>Next:</p>";
    /// DO NOT USE UNLESS HACKED TO RETURN
    md2_send_meeting_mail($dr);    
    $drs = md2_get_active_doctor_list();
    echo "<ul>";
    foreach ($drs as $doc)
    {
      echo "<li>".$doc."</li>";
    }
    echo "</ul>";
    ?>
  </body>
</html>
