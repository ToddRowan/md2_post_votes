<!DOCTYPE html>
<html>
<?php
require("../../../../wp-load.php");
require("test_includes.php");
    
$dr1 = 1;
$dr2 = 2;
$dr3 = 3;
$dr4 = 4;

$ds1 = "2014-02-14 12:45:17";
$ds2 = "2015-02-15 05:14:59";
$ds3 = "2016-03-31 18:32:00";
$ds4 = "2015-04-12 01:01:01";

$post2 = 2;
$post1 = 1;
$post3 = 3;
$post4 = 4;

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
        <h2>Create eligibles</h2>
        <?php
            echo "<p>Post $post1 is eligible for range $dr1 with date $ds1.</p>";
            md2_add_eligible_post($post1, $dr1, $ds1);
            
            echo "<p>Post $post2 is eligible for range $dr2 with date $ds2.</p>";
            md2_add_eligible_post($post2, $dr2, $ds2);
            
            echo "<p>Post $post3 is eligible for range $dr3 with date $ds3.</p>";
            md2_add_eligible_post($post3, $dr3, $ds3);
            
            echo "<p>Post $post4 is eligible for range $dr4 with date $ds4.</p>";
            md2_add_eligible_post($post4, $dr4, $ds4);
            echo "<p>Post $post3 is eligible for range $dr4 with date $ds4.</p>";
            md2_add_eligible_post($post3, $dr4, $ds4);
            
            echo "<p>Trying post $post4 is eligible for range $dr4 with date $ds4 again.</p>";
            md2_add_eligible_post($post4, $dr4, $ds4);
        ?>
        <h2>Eligible post retrieval</h2>
        <?php
        
            $drs =  array($dr1, $dr2, $dr3, $dr4);
            
            foreach ($drs as $dr)
            {
                echo "<p>Getting posts in date range $dr:</p>";
                $ps = md2_get_eligible_posts_by_date_range($dr);
                echo "<p>print_r". print_r($ps,true). "</p>";
                foreach ($ps as $p)
                {
                    echo "<p>Post id is: " . $p->post_id;
                }

                echo "<p>Done</p>";
            }
            
            echo "<p>Eligible posts existing: " . count_eligible_posts() ."</p>";
        ?>
        
        <h2>Selecting selected posts for review</h2>
        <?php
            echo "<p>Getting selected posts in range $dr4.</p>";
            md2_set_eligible_post_selection_status($post4, $dr4);
            $eligible_posts = md2_get_selected_eligible_posts($dr4);
            foreach($eligible_posts as $ep)
            {
                echo "<p>Post number $ep is selected</p>";
            }
            echo "<p>Done.</p>";
            
            echo "<p>Getting unselected posts in range $dr4.</p>";
            $eligible_posts = md2_get_unselected_eligible_posts($dr4);
            foreach($eligible_posts as $ep)
            {
                echo "<p>Post number $ep is not selected</p>";
            }
            echo "<p>Done.</p>";
        ?>
        
        <h2>Eligible posts delete</h2>
        <p>Killing off all eligible posts</p>
        <?php
            md2_delete_eligible_post_by_id($post1);
            md2_delete_eligible_post_by_date_range($dr2);
            md2_delete_eligible_post_by_id_and_date_range($post3, $dr3);
            md2_delete_eligible_post_by_id_and_date_range($post4, $dr4);
            md2_delete_eligible_post_by_date_range($dr4);
            echo "<p>Eligible posts existing: " . count_eligible_posts() ."</p>";        
        ?>
</html>
