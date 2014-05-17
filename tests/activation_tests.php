<?php
require("../../../../wp-load.php");
require("test_includes.php");
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h2>Test activatable logic</h2>
        <?php
            foreach (md2_get_all_date_ranges() as $dr)
            {
                echo "<p>Range {$dr->id} is " . (md2_is_date_range_activatable($dr->id)?"":"not ") . "activatable</p>";
            }
        ?>
    </body>
</html>
