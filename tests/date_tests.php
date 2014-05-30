<!DOCTYPE html>
<html>
<?php
require("../../../../wp-load.php");
require("test_includes.php");
    
$ds1 = "2/12/2014";
$ds2 = "2/15/2015";
$de1 = "3/31/2016";
$de2 = "4/12/2015";

$dfpairs = array(
    array("2/12/2012","2/25/2011"),
    array("2/25/2011","2/12/2012"),
    array("2/12/2012","1/12/2012"),
    array("1/12/2012","2/12/2012"),
    array("5/15/2013","5/14/2013"),
    array("5/14/2013","5/15/2013"),
    array("2/8/1985","2/8/1985")
);

?>
    <head>
        
    </head>
    <body>
        <h1>Starting tests</h1>
        <?php 
            echo "<p>About to clear timeframe table.</p>";
            clear_date_ranges_table();
            echo "<p>Ranges remaining: " . count_date_ranges() ."</p>";
        ?>
        <h2>Create date ranges</h2>
        <?php
            echo "<p>From $ds1 to $de1:</p>";
            $newid1 = md2_create_vote_date_range($ds1, $de1);
            echo "new ID: " . $newid1;
            
            echo "<p>From $ds2 to $de2:</p>";
            $newid2 = md2_create_vote_date_range($ds2, $de2);
            echo "new ID: " . $newid2;
        ?>
        <h2>Update date ranges</h2>
        <?php
            echo "<p>Updating $newid1:</p>";
            $er = md2_update_vote_date_range($newid1, array("process_state"=>512));
            echo "result: " . print_r($er,true);
            
            echo "<p>Updating $newid2:</p>";
            $er = md2_update_vote_date_range($newid2, array("process_state"=>1024,"is_locked"=>'y'));
            echo "result: " . print_r($er,true);
            
            
        ?>
        <h2>Test latest date retrieval</h2>
        <?php
            echo "<p>Latest date: ($de1) = " . md2_get_latest_end_date();
            echo "<p>Latest date plus one: ($de1) = " . get_latest_end_date_plus_one();
        ?>
        
        <h2>Test date comparators</h2>
        <?php
            foreach ($dfpairs as $p)
            {
                echo "<p>Is " . $p[0] . " earlier than " . $p[1] . "? " . (is_date_earlier(date_parse($p[0]), date_parse($p[1]))?"YES":"NO") ."</p>";
            }
        ?>
</html>
