<!DOCTYPE html>
<html>
<?php
require("../../../../wp-load.php");
require("test_includes.php");

$post2 = 2;
$post5 = 5;
$post9 = 9;
$post22 = 22;

$user2 = 2;
$user5 = 5;
$user8 = 8;
$user55 = 55;

$tf1 = 2;
$tf2 = 4;
$tf3 = 8;

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
            set_vote($post2, $user2,$tf1);
            echo "<p>User $user5 votes for post $post5.</p>";
            set_vote($post5, $user5, $tf1); 
            echo "<p>User $user55 votes for post $post5.</p>";
            set_vote($post5, $user55,$tf1);
            echo "<p>User $user2 votes for post $post22.</p>";
            set_vote($post22, $user2,$tf1);
            echo "<p>Votes created: " . count_votes() ."</p>";
        ?>
        <h2>Checking votes for posts</h2>
        <?php
            echo "<p>Did user $user2 vote for $post2 (yes)--";
            echo (did_user_vote_for_post($post2, $user2, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user5 vote for $post5 (yes)--";
            echo (did_user_vote_for_post($post5, $user5, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user55 vote for $post5 (yes)--";
            echo (did_user_vote_for_post($post5, $user55, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user2 vote for $post22 (yes)--";
            echo (did_user_vote_for_post($post22, $user2, $tf1)?"YES":"NO")."</p>";
            
            echo "<p>Did user $user5 vote for $post9 (no)--";
            echo (did_user_vote_for_post($post9, $user5, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user5 vote for $post2 (no)--";
            echo (did_user_vote_for_post($post2, $user5, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user8 vote for $post2 (no)--";
            echo (did_user_vote_for_post($post2, $user8, $tf1)?"YES":"NO")."</p>";
            echo "<p>Did user $user55 vote for $post2 (no)--";
            echo (did_user_vote_for_post($post2, $user55, $tf1)?"YES":"NO")."</p>";
        ?>
        <h3>Counting votes for posts</h3>
        <?php
            echo "<p>How many users voted for $post5 (2)--";
            echo get_vote_count_for_post($post5, $tf1). "</p>";
            echo "<p>How many users voted for $post22 (1)--";
            echo get_vote_count_for_post($post22, $tf1). "</p>";
            echo "<p>How many users voted for $post9 (0)--";
            echo get_vote_count_for_post($post9, $tf1). "</p>";
        ?>        
        <?php
            echo "<p>How many posts voted on by user $user2 (2)--";
            echo get_vote_count_for_user($user2, $tf1). "</p>";
            echo "<p>How many posts voted on by user $user55 (1)--";
            echo get_vote_count_for_user($user55, $tf1). "</p>";
            echo "<p>How many posts voted on by user $user8 (0)--";
            echo get_vote_count_for_user($user8, $tf1). "</p>";
        ?>
        
        <h3>Deleting votes for posts</h3>
        <?php
            echo "<p>Deleting votes for post $post22 (1)--";
            echo get_vote_count_for_post($post22, $tf1). "</p>";
            delete_votes_by_post($post22, $tf1);
            echo "<p>How many votes now for post $post22 (0)--";
            echo get_vote_count_for_post($post22, $tf1). "</p>";
            
            echo "<p>Deleting votes for post $post9 (0)--";
            echo get_vote_count_for_post($post9, $tf1). "</p>";
            delete_votes_by_post($post9, $tf1);
            echo "<p>How many votes now for post $post9 (0)--";
            echo get_vote_count_for_post($post9, $tf1). "</p>";
        ?>        
        <?php
            echo "<p>Deleting votes for user $user2 (1)--";
            echo get_vote_count_for_user($user2, $tf1). "</p>";
            delete_votes_by_user($user2, $tf1);
            echo "<p>How many posts now voted by user $user2 (0)--";
            echo get_vote_count_for_user($user2, $tf1). "</p>";
            
            echo "<p>Deleting likes for user $user55 (1)--";
            echo get_vote_count_for_user($user55, $tf1). "</p>";
            delete_votes_by_user($user55, $tf1);
            echo "<p>How many posts now voted $user55 (0)--";
            echo get_vote_count_for_user($user55, $tf1). "</p>";
            
            echo "<p>Deleting likes for user $user8 (0)--";
            echo get_vote_count_for_user($user8, $tf1). "</p>";
            delete_votes_by_user($user8, $tf1);
            echo "<p>How many posts now voted $user8 (0)--";
            echo get_vote_count_for_user($user8, $tf1). "</p>";
        ?>
        <h2> Creating dupe votes</h2>
        <?php
            echo "<p>User $user2 votes for post $post2.</p>";
            set_vote($post2, $user2, $tf1);
            echo "<p>Vote count for post  $post2: " . get_vote_count_for_post($post2, $tf1) . "</p>";
            echo "<p>User $user2 votes for post $post2 again.</p>";
            set_vote($post2, $user2, $tf1);
            echo "<p>Vote count for post  $post2: " . get_vote_count_for_post($post2, $tf1) . "</p>";
        ?>   
    </body>
</html>