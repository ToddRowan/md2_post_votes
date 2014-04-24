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

$suggestion1 = "This is suggestion 1";
$suggestion2 = "This is suggestion two";
$suggestion3 = "This is suggestion three";
$suggestion4 = "This is suggestion four";

?>
    <head>
        
    </head>
    <body>
        <h1>Starting tests</h1>
        <?php 
            echo "<p>About to clear vote suggestion table.</p>";
            clear_vote_suggestion_table();
            echo "<p>Suggestions remaining: " . count_suggestions() ."</p>";
        ?>
        <h2>Create suggestions</h2>
        <?php
            echo "<p>User $user1 says $suggestion1 for range $dr1.</p>";
            md2_create_vote_suggestion($user1, $dr1, $suggestion1);
            
            echo "<p>User $user2 says $suggestion2 for range $dr2.</p>";
            md2_create_vote_suggestion($user2, $dr2, $suggestion2);
            
            echo "<p>User $user3 says $suggestion3 for range $dr3.</p>";
            md2_create_vote_suggestion($user3, $dr3, $suggestion3);
            
            echo "<p>User $user4 says $suggestion4 for range $dr4.</p>";
            md2_create_vote_suggestion($user4, $dr4, $suggestion4);
        ?>
        <h2>Test suggestion retrieval</h2>
        <?php
            echo "<p>User $user1 said this about range $dr1:</p>";
            echo "<p>".md2_get_vote_suggestion($user1, $dr1)."</p>";
            echo "<p>User $user2 said this about range $dr2:</p>";
            echo "<p>".md2_get_vote_suggestion($user2, $dr2)."</p>";
            echo "<p>User $user3 said this about range $dr3:</p>";
            echo "<p>".md2_get_vote_suggestion($user3, $dr3)."</p>";
            echo "<p>User $user4 said this about range $dr4:</p>";
            echo "<p>".md2_get_vote_suggestion($user4, $dr4)."</p>";
            
            echo "<p>User $user1 now says 'You suck via update' about about range $dr1:</p>";
            md2_update_vote_suggestion($user1, $dr1, 'You suck via update');
            echo "<p>User $user1 said this about range $dr1:</p>";
            echo "<p>".md2_get_vote_suggestion($user1, $dr1)."</p>";
            
            echo "<p>User $user1 now says 'You suck via create' about about range $dr1:</p>";
            md2_create_vote_suggestion($user1, $dr1, 'You suck via create');
            echo "<p>User $user1 said this about range $dr1:</p>";
            echo "<p>".md2_get_vote_suggestion($user1, $dr1)."</p>";
            
            echo "<p>Suggestions existing: " . count_suggestions() ."</p>";
        ?>
        
        <h2>Test suggestions delete</h2>
        <p>Killing off all suggestions</p>
        <?php
        md2_delete_vote_suggestion($user1, $dr1);
        md2_delete_vote_suggestions_by_user($user3);
         md2_delete_vote_suggestions_by_user($user2);
        md2_delete_vote_suggestions_by_date_range($dr4);
        echo "<p>Suggestions existing: " . count_suggestions() ."</p>";
        
        ?>
        <h2>Testing get on non-existent id set:</h2>
        <?php
        $fake = md2_get_vote_suggestion($user1, $dr3);
        echo "<p>fake id is " . (is_null($fake)?"null":"not null") . "</p>";
        ?>
</html>
