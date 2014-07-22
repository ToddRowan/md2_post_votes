<!DOCTYPE html>
<html>
<?php
require("../../../../wp-load.php");
require("test_includes.php");

$user1 = 1;
$user2 = 2;
$user3 = 3;

$dr2 = 2;
$dr1 = 1;
$dr3 = 3;

?>
    <head>
        
    </head>
    <body>
        <h1>Starting tests</h1>
        <?php 
            echo "<p>About to clear date range votes table.</p>";
            clear_date_range_votes_table();
            echo "<p>Votes remaining: " . count_date_range_votes() ."</p>";
        ?>
        <h2>Create votes for date ranges</h2>
        <?php
            echo "<p>Creating four votes for date ranges.</p>";
            echo "<p>User $user2 votes for date range $dr2.</p>";
            md2_add_date_range_vote($dr2,$user2);
            echo "<p>User $user1 votes for date range $dr2.</p>";
            md2_add_date_range_vote($dr2,$user1); 
            echo "<p>User $user2 votes for date range $dr3.</p>";
            md2_add_date_range_vote($dr3,$user2);
            echo "<p>Votes created: " . count_date_range_votes() ."</p>";
        ?>
        <h3>Counting votes for date ranges</h3>
        <?php
            echo "<p>How many users voted for $dr1 (0)--";
            echo md2_get_vote_count_for_date_range($dr1). "</p>";
            echo "<p>How many users voted for $dr2 (2)--";
            echo md2_get_vote_count_for_date_range($dr2). "</p>";
            echo "<p>How many users voted for $dr3 (1)--";
            echo md2_get_vote_count_for_date_range($dr3). "</p>";
        ?>
        
        <h3>Checking if users voted</h3>
        <?php
            echo "<p>Did $user1 vote for $dr1 (no)--";
            echo (md2_did_user_vote_for_date_range($dr1, $user1)?"Yes":"No")."<p/>";
            echo "<p>Did $user2 vote for $dr2 (yes)--";
            echo (md2_did_user_vote_for_date_range($dr2, $user2)?"Yes":"No")."<p/>";
            echo "<p>Did $user1 vote for $dr2 (yes)--";
            echo (md2_did_user_vote_for_date_range($dr2, $user1)?"Yes":"No")."<p/>";
        ?>
        
        <h3>Listing users who voted for ranges</h3>
        <?php 
          $drarr = array($dr1,$dr2,$dr3);
          foreach ($drarr as $drid)
          {
            echo "<p>Voters for $drid:</p>";
            ?>
        <table>
          <tr><th>Voters</th></tr>
        <?php
          $votes = md2_get_date_range_votes_by_date_range($drid);
          if (count($votes)==0)
          {
            echo "<tr><td>No votes</td></tr>";
          }
          else
          {
            foreach ($votes as $vote)
            {
              echo "<tr><td>".$vote."</td></tr>";
            }
          }
        ?>
        </table>
          <?php   }?>        
        
        <h3>Deleting votes for date ranges</h3>
        <?php       
            echo "<p>Deleting $user2 vote for post $dr2.";
            md2_delete_date_range_vote($dr2, $user2);
            echo "<p>Did $user2 vote for $dr2 (no)--";
            echo (md2_did_user_vote_for_date_range($dr2, $user2)?"Yes":"No") . "</p>";
        ?>
        <h2> Creating dupe votes</h2>
        <?php
          echo "<p>this many votes for $user2 voting for date range $dr1 (0): " . count(md2_get_date_range_votes_by_date_range($dr1)) . "</p>";
          echo "<p>User $user2 votes for date range $dr1.</p>";
          md2_add_date_range_vote($dr1,$user2);
          echo "<p>this many votes for that combo (1): " . count(md2_get_date_range_votes_by_date_range($dr1)) . "</p>";
          echo "<p>User $user2 votes for date range $dr1 again.</p>";
          md2_add_date_range_vote($dr1,$user2);
          echo "<p>this many votes for that combo (1): " . count(md2_get_date_range_votes_by_date_range($dr1)) . "</p>";
        ?>   
    </body>
</html>