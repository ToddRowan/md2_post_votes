<!DOCTYPE html>
<html>
<?php
require("../../../../wp-load.php");
require("test_includes.php");

$post = 2;
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
            set_vote($post5, $user5,$tf1); 
            echo "<p>User $user55 votes for post $post5.</p>";
            set_vote($post5, $user55,$tf1);
            echo "<p>User $user2 votes for post $post22.</p>";
            set_vote($post22, $user2,$tf1);
            echo "<p>Likes created: " . count_votes() ."</p>";
        ?>
        <h2>Checking votes for posts</h2>
        <?php
            echo "<p>Did user $user2 vote for $post2 (yes)--";
            echo (does_user_like_post($post2, $user2)?"YES":"NO")."</p>";
            echo "<p>Did user $user5 vote for $post5 (yes)--";
            echo (does_user_like_post($post5, $user5)?"YES":"NO")."</p>";
            echo "<p>Did user $user55 vote for $post5 (yes)--";
            echo (does_user_like_post($post5, $user55)?"YES":"NO")."</p>";
            echo "<p>Did user $user2 vote for $post22 (yes)--";
            echo (does_user_like_post($post22, $user2)?"YES":"NO")."</p>";
            
            echo "<p>Did user $user5 vote for $post9 (no)--";
            echo (does_user_like_post($post9, $user5)?"YES":"NO")."</p>";
            echo "<p>Did user $user5 vote for $post2 (no)--";
            echo (does_user_like_post($post2, $user5)?"YES":"NO")."</p>";
            echo "<p>Did user $user8 vote for $post2 (no)--";
            echo (does_user_like_post($post2, $user8)?"YES":"NO")."</p>";
            echo "<p>Did user $user55 vote for $post2 (no)--";
            echo (does_user_like_post($post2, $user55)?"YES":"NO")."</p>";
        ?>
        <h3>Counting votes for posts</h3>
        <?php
            echo "<p>How many users voted for $post5 (2)--";
            echo get_user_like_count_for_post($post5). "</p>";
            echo "<p>How many users voted for $post22 (1)--";
            echo get_user_like_count_for_post($post22). "</p>";
            echo "<p>How many users voted for $post9 (0)--";
            echo get_user_like_count_for_post($post9). "</p>";
        ?>        
        <?php
            echo "<p>How many posts voted on by user $user2 (2)--";
            echo get_post_like_count_for_user($user2). "</p>";
            echo "<p>How many posts voted on by user $user55 (1)--";
            echo get_post_like_count_for_user($user55). "</p>";
            echo "<p>How many posts voted on by user $user8 (0)--";
            echo get_post_like_count_for_user($user8). "</p>";
        ?>
        
        <h3>Deleting votes for posts</h3>
        <?php
            echo "<p>Deleting votes for post $post22 (1)--";
            echo get_user_like_count_for_post($post22). "</p>";
            delete_likes_by_post($post22);
            echo "<p>How many votes now for post $post22 (0)--";
            echo get_user_like_count_for_post($post22). "</p>";
            
            echo "<p>Deleting votes for post $post9 (0)--";
            echo get_user_like_count_for_post($post9). "</p>";
            delete_likes_by_post($post9);
            echo "<p>How many votes now for post $post9 (0)--";
            echo get_user_like_count_for_post($post9). "</p>";
        ?>        
        <?php
            echo "<p>Deleting votes for user $user2 (1)--";
            echo get_post_like_count_for_user($user2). "</p>";
            delete_likes_by_user($user2);
            echo "<p>How many posts now liked by user $user2 (0)--";
            echo get_post_like_count_for_user($user2). "</p>";
            
            echo "<p>Deleting likes for user $user55 (1)--";
            echo get_post_like_count_for_user($user55). "</p>";
            delete_likes_by_user($user55);
            echo "<p>How many users now like $user55 (0)--";
            echo get_post_like_count_for_user($user55). "</p>";
            
            echo "<p>Deleting likes for user $user8 (0)--";
            echo get_post_like_count_for_user($user8). "</p>";
            delete_likes_by_user($user8);
            echo "<p>How many users now like $user8 (0)--";
            echo get_post_like_count_for_user($user8). "</p>";
        ?>
        <h2> Creating dupe likes</h2>
        <?php
            echo "<p>User $user2 likes post $post2.</p>";
            create_like($post2, $user2);
            echo "<p>User $user2 likes post $post2 again.</p>";
            create_like($post2, $user2);
        ?>        
        
        <h2>Create likes for comments</h2>
        <?php
            echo "<p>Creating four likes.</p>";
            echo "<p>User $user2 likes comment $post2.</p>";
            create_like($post2, $user2, 'comment');
            echo "<p>User $user5 likes comment $post5.</p>";
            create_like($post5, $user5, 'comment'); 
            echo "<p>User $user55 likes comment $post5.</p>";
            create_like($post5, $user55, 'comment');
            echo "<p>User $user2 likes comment $post22.</p>";
            create_like($post22, $user2, 'comment');
            echo "<p>Likes created: " . count_comment_likes() ."</p>";
        ?>
        <h2>Checking likes for comments</h2>
        <?php
            echo "<p>Does user $user2 like $post2 (yes)--";
            echo (does_user_like_comment($post2, $user2)?"YES":"NO")."</p>";
            echo "<p>Does user $user5 like $post5 (yes)--";
            echo (does_user_like_comment($post5, $user5)?"YES":"NO")."</p>";
            echo "<p>Does user $user55 like $post5 (yes)--";
            echo (does_user_like_comment($post5, $user55)?"YES":"NO")."</p>";
            echo "<p>Does user $user2 like $post22 (yes)--";
            echo (does_user_like_comment($post22, $user2)?"YES":"NO")."</p>";
            
            echo "<p>Does user $user5 like $post9 (no)--";
            echo (does_user_like_comment($post9, $user5)?"YES":"NO")."</p>";
            echo "<p>Does user $user5 like $post2 (no)--";
            echo (does_user_like_comment($post2, $user5)?"YES":"NO")."</p>";
            echo "<p>Does user $user8 like $post2 (no)--";
            echo (does_user_like_comment($post2, $user8)?"YES":"NO")."</p>";
            echo "<p>Does user $user55 like $post2 (no)--";
            echo (does_user_like_comment($post2, $user55)?"YES":"NO")."</p>";
        ?>
        <h3>Counting likes for comments</h3>
        <?php
            echo "<p>How many users like $post5 (2)--";
            echo get_user_like_count_for_comment($post5). "</p>";
            echo "<p>How many users like $post22 (1)--";
            echo get_user_like_count_for_comment($post22). "</p>";
            echo "<p>How many users like $post9 (0)--";
            echo get_user_like_count_for_comment($post9). "</p>";
        ?>        
        <?php
            echo "<p>How many comments liked by user $user2 (2)--";
            echo get_comment_like_count_for_user($user2). "</p>";
            echo "<p>How many comments liked by user $user55 (1)--";
            echo get_comment_like_count_for_user($user55). "</p>";
            echo "<p>How many comments liked by user $user8 (0)--";
            echo get_comment_like_count_for_user($user8). "</p>";
        ?>
        
        <h3>Deleting likes</h3>
        <?php
            echo "<p>Deleting likes for comment $post22 (1)--";
            echo get_user_like_count_for_comment($post22). "</p>";
            delete_likes_by_comment($post22);
            echo "<p>How many users now like comment $post22 (0)--";
            echo get_user_like_count_for_comment($post22). "</p>";
            
            echo "<p>Deleting likes for comment $post9 (0)--";
            echo get_user_like_count_for_comment($post9). "</p>";
            delete_likes_by_comment($post9);
            echo "<p>How many users now like comment $post9 (0)--";
            echo get_user_like_count_for_comment($post9). "</p>";
        ?>        
        <?php
            echo "<p>Deleting comment likes for user $user2 (1)--";
            echo get_comment_like_count_for_user($user2). "</p>";
            delete_likes_by_user($user2);
            echo "<p>How many comments now liked by user $user2 (0)--";
            echo get_comment_like_count_for_user($user2). "</p>";
            
            echo "<p>Deleting comment likes for user $user55 (1)--";
            echo get_comment_like_count_for_user($user55). "</p>";
            delete_likes_by_user($user55);
            echo "<p>How many users now like $user55 (0)--";
            echo get_comment_like_count_for_user($user55). "</p>";
            
            echo "<p>Deleting comment likes for user $user8 (0)--";
            echo get_comment_like_count_for_user($user8). "</p>";
            delete_likes_by_user($user8);
            echo "<p>How many users now like $user8 (0)--";
            echo get_comment_like_count_for_user($user8). "</p>";
        ?>
        <h2> Creating dupe likes</h2>
        <?php
            echo "<p>User $user2 likes comment $post2.</p>";
            create_like($post2, $user2, 'comment');
            echo "<p>User $user2 likes comment $post2 again.</p>";
            create_like($post2, $user2, 'comment');
        ?>
        
        
        
    </body>
</html>