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
        echo "<tr>";
        echo "<td>";

        echo "<table width=\"25%\">";
        echo "<tr>";
        echo '<td><img src="' .  APP_DOMAIN . "images/" . $record['image_name'] . '" height=300></td>';
        echo "</tr>";

        echo "<tr>";
        echo '<td style="text-align:center"><b>' . $record['code'] . '</b></td>';
        echo "</tr>";

        echo "<tr>";
        echo '<td style="text-align:center">' . $record['name'] . '</td>';
        echo "</tr>";

        echo "<tr>";
        echo '<td style="text-align:center; font-size: 16px;"> ' . CURRENCY_SYMBOL . ' ' . Convert::toNum( $record['cost']) . '</td>';
        echo "</tr>";

        echo "</table>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
</tbody>