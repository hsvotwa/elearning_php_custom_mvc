<?php
if( ! $records || ! $records->num_rows ) {
    echo "<thead id=\"th_cont_data\"><tr><td>No records found</td></tr></thead>";
    return;
}
?>
<tbody id="tb_cont_data">
    <?php
    $count = 0;
    $action = $can_edit ? "edit" : "detail";
    foreach ( $records as $record ) {
        if( $count % 4 == 0 ) {
            if( $count > 0 ) {
                echo "</tr>";
            }
            echo "<tr>";
        }
        $count ++;
        echo "<td>";

        echo "<table width=\"25%\">";
        echo "<tr>";
        echo '<td><img src="' .  APP_DOMAIN . "images/" . $record['image_name'] . '" width=300></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td>' . $record['name'] . '</td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td><a href="' . WEBROOT . "ebooks/" . $record['file_name'] . '" download targer="_blank">Download eBook</a></td>';
        echo "</tr>";

        echo "</table>";
        echo "</td>";
    }
    echo "</tr>";
    ?>
</tbody>