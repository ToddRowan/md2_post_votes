<!DOCTYPE html>
<html>
<?php
require("../../../../wp-load.php");
require("test_includes.php");

$post1 = 1;
$post2 = 2;
$post5 = 5;
$post17 = 17;
$post20 = 20;

$user2 = 2;
$user1 = 1;
$user3 = 3;

$tf1 = 3;
?>
    <head>
        
    </head>
    <body>
        <h1>Starting tests</h1>
        <?php 
            echo "<p>About to clear votes table.</p>";
            clear_votes_table();
            echo "<p>Votes remaining: " . count_votes() ."</p>";
        ?>
        <h2>Create votes for posts</h2>
        <?php
            echo "<p>Creating four votes for posts.</p>";
            echo "<p>User $user2 votes for post $post2.</p>";
            md2_set_vote($post2, $user2,$tf1);
            echo "<p>User $user1 votes for post $post5.</p>";
            md2_set_vote($post5, $user1, $tf1); 
            echo "<p>User $user2 votes for post $post5.</p>";
            md2_set_vote($post5, $user2,$tf1);
            echo "<p>User $user2 votes for post $post20.</p>";
            md2_set_vote($post20,$user2,$tf1);
            echo "<p>Votes created: " . count_votes() ."</p>";
        ?>
        <h2>Checking votes for posts</h2>
        <?php /*
            echo "<p>Did user $user2 vote for $post2 (yes)--";
            echo (did_user_vote_for_post($post2, $user2, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user1 vote for $post5 (yes)--";
            echo (did_user_vote_for_post($post5, $user1, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user2 vote for $post5 (yes)--";
            echo (did_user_vote_for_post($post5, $user2, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user2 vote for $post20 (yes)--";
            echo (did_user_vote_for_post($post20, $user2, $tf1)?"YES":"NO")."</p>";
            
            echo "<p>Did user $user5 vote for $post9 (no)--";
            echo (did_user_vote_for_post($post9, $user5, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user5 vote for $post2 (no)--";
            echo (did_user_vote_for_post($post2, $user5, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user8 vote for $post2 (no)--";
            echo (did_user_vote_for_post($post2, $user8, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user55 vote for $post2 (no)--";
            echo (did_user_vote_for_post($post2, $user55, $tf1)?"YES":"NO")."</p>"; */
        ?>
        <h3>Counting votes for posts</h3>
        <?php
            echo "<p>How many users voted for $post5 (2)--";
            echo get_vote_count_for_post($post5, $tf1). "</p>";
            echo "<p>How many users voted for $post20 (1)--";
            echo get_vote_count_for_post($post20, $tf1). "</p>";
            echo "<p>How many users voted for $post17 (0)--";
            echo get_vote_count_for_post($post17, $tf1). "</p>";
        ?>        
        <?php
            echo "<p>How many posts voted on by user $user2 (3)--";
            echo get_vote_count_for_user($user2, $tf1). "</p>";
            echo "<p>How many posts voted on by user $user1 (1)--";
            echo get_vote_count_for_user($user1, $tf1). "</p>";
            echo "<p>How many posts voted on by user $user3 (0)--";
            echo get_vote_count_for_user($user3, $tf1). "</p>";
        ?>
        
        <h3>Ranking posts by votes</h3>
        <table>
          <tr><th>Title</th><th>Votes</th></tr>
        <?php
          $votes = md2_get_post_vote_counts($tf1);
          if (count($votes)==0)
          {
            echo "<p>No votes</p>";
          }
          foreach ($votes as $vote)
          {
            echo "<tr><td>".$vote->post_title."</td><td>".$vote->votecount."</td></tr>";
          }
        ?>
        </table>
        
        <h3>Ranking users by votes</h3>
        <table>
          <tr><th>User</th><th>Votes</th></tr>
        <?php
          $votes = md2_get_user_vote_counts($tf1);
          if (count($votes)==0)
          {
            echo "<p>No results</p>";
          }
          foreach ($votes as $vote)
          {
            echo "<tr><td>".$vote->user_login."</td><td>".$vote->votecount."</td></tr>";
          }
        ?>
        </table>
        <h3>Deleting votes for posts</h3>
        <?php
        /*
            echo "<p>Deleting votes for post $post22 (1)--";
            echo get_vote_count_for_post($post22, $tf1). "</p>";
            md2_delete_votes_by_post($post22, $tf1);
            echo "<p>How many votes now for post $post22 (0)--";
            echo get_vote_count_for_post($post22, $tf1). "</p>";
            
            echo "<p>Deleting votes for post $post9 (0)--";
            echo get_vote_count_for_post($post9, $tf1). "</p>";
            md2_delete_votes_by_post($post9, $tf1);
            echo "<p>How many votes now for post $post9 (0)--";
            echo get_vote_count_for_post($post9, $tf1). "</p>";
        
            echo "<p>Deleting votes for user $user2 (1)--";
            echo get_vote_count_for_user($user2, $tf1). "</p>";
            md2_delete_votes_by_user($user2, $tf1);
            echo "<p>How many posts now voted by user $user2 (0)--";
            echo get_vote_count_for_user($user2, $tf1). "</p>";
            
            echo "<p>Deleting likes for user $user55 (1)--";
            echo get_vote_count_for_user($user55, $tf1). "</p>";
            md2_delete_votes_by_user($user55, $tf1);
            echo "<p>How many posts now voted $user55 (0)--";
            echo get_vote_count_for_user($user55, $tf1). "</p>";
            
            echo "<p>Deleting likes for user $user8 (0)--";
            echo get_vote_count_for_user($user8, $tf1). "</p>";
            md2_delete_votes_by_user($user8, $tf1);
            echo "<p>How many posts now voted $user8 (0)--";
            echo get_vote_count_for_user($user8, $tf1). "</p>";
         */
        ?>
        <h2> Creating dupe votes</h2>
        <?php
            echo "<p>User $user2 votes for post $post2.</p>";
            md2_set_vote($post2, $user2, $tf1);
            echo "<p>Vote count for post  $post2: " . get_vote_count_for_post($post2, $tf1) . "</p>";
            echo "<p>User $user2 votes for post $post2 again.</p>";
            md2_set_vote($post2, $user2, $tf1);
            echo "<p>Vote count for post  $post2: " . get_vote_count_for_post($post2, $tf1) . "</p>";
        ?>   
    </body>
</html>