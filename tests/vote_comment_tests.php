<!DOCTYPE html>
<html>
<?php
require("../../../../wp-load.php");
require("test_includes.php");
    
$dr1 = 1;
$dr2 = 2;
$dr3 = 3;
$dr4 = 4;

$user1 = 1;
$user2 = 2;
$user3 = 3;
$user4 = 4;

$post2 = 2;
$post1 = 1;
$post3 = 3;
$post4 = 4;

$comment1 = "This is comment 1";
$comment2 = "This is comment two";
$comment3 = "This is comment three";
$comment4 = "This is comment four";

?>
    <head>
        
    </head>
    <body>
        <h1>Starting tests</h1>
        <?php 
            echo "<p>About to clear vote comment table.</p>";
            clear_vote_comment_table();
            echo "<p>Comments remaining: " . count_comments() ."</p>";
        ?>
        <h2>Create comments</h2>
        <?php
            echo "<p>User $user1 says $comment1 about post $post1 in range $dr1.</p>";
            md2_create_vote_comment($user1, $post1, $dr1, $comment1);
            
            echo "<p>User $user2 says $comment2 about post $post2 in range $dr2.</p>";
            md2_create_vote_comment($user2, $post2, $dr2, $comment2);
            
            echo "<p>User $user3 says $comment3 about post $post3 in range $dr3.</p>";
            md2_create_vote_comment($user3, $post3, $dr3, $comment3);
            
            echo "<p>User $user4 says $comment4 about post $post4 in range $dr4.</p>";
            md2_create_vote_comment($user4, $post4, $dr4, $comment4);
        ?>
        <h2>Test comment retrieval</h2>
        <?php
            echo "<p>User $user1 said this about $post1 in range $dr1:</p>";
            echo "<p>".md2_get_vote_comment($user1, $post1, $dr1)."</p>";
            echo "<p>User $user2 said this about $post2 in range $dr2:</p>";
            echo "<p>".md2_get_vote_comment($user2, $post2, $dr2)."</p>";
            echo "<p>User $user3 said this about $post3 in range $dr3:</p>";
            echo "<p>".md2_get_vote_comment($user3, $post3, $dr3)."</p>";
            echo "<p>User $user4 said this about $post4 in range $dr4:</p>";
            echo "<p>".md2_get_vote_comment($user4, $post4, $dr4)."</p>";
            
            echo "<p>User $user1 now says 'You suck via update' about about $post1 in range $dr1:</p>";
            md2_update_vote_comment($user1, $post1, $dr1, 'You suck via update');
            echo "<p>User $user1 said this about $post1 in range $dr1:</p>";
            echo "<p>".md2_get_vote_comment($user1, $post1, $dr1)."</p>";
            
            echo "<p>User $user1 now says 'You suck via create' about about $post1 in range $dr1:</p>";
            md2_create_vote_comment($user1, $post1, $dr1, 'You suck via create');
            echo "<p>User $user1 said this about $post1 in range $dr1:</p>";
            echo "<p>".md2_get_vote_comment($user1, $post1, $dr1)."</p>";
            
            echo "<p>Comments existing: " . count_comments() ."</p>";
        ?>
        
        <h2>Test comments delete</h2>
        <p>Killing off all comments</p>
        <?php
        md2_delete_vote_comment($user1, $post1, $dr1);
        md2_delete_vote_comments_by_post($post2);
        md2_delete_vote_comments_by_user($user3);
        md2_delete_vote_comments_by_date_range($dr4);
        echo "<p>Comments existing: " . count_comments() ."</p>";
        
        ?>
        <h2>Testing get on non-existent id set:</h2>
        <?php
        $fake = md2_get_vote_comment($user1, $post2, $dr3);
        echo "<p>fake id is " . (is_null($fake)?"null":"not null") . "</p>";
        ?>
</html>
