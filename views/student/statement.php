<?php
if( ! $records ) {
    echo "<thead id=\"th_cont_data\"><tr><td>No records found</td></tr></thead>";
    return;
}
?>
<tbody id="tb_cont_data">
    <?php
        echo "<tr>";
            echo '<td>';
            echo '<td style="text-align:center">Subject cost: <b>' . $record['subject_cost'] . '</b></td>';
            echo '<td style="text-align:center">Study aids cost: <b>' . $record['aid_cost'] . '</b></td>';
            echo '<td style="text-align:center">Total cost: <b>' . $record['total_cost'] . '</b></td>';
            echo '<td style="text-align:center">Interest percentage cost: <b>' . $record['interest_percent'] . '</b></td>';
            echo '<td style="text-align:center">Interest amount: <b>' . $record['interest_amount'] . '</b></td>';
            echo '<td style="text-align:center">Total due: <b>' . $record['total_due'] . '</b></td>';
            echo '<td style="text-align:center">Monthly payment: <b>' . $record['monthly_payment'] . '</b></td>';
            echo "</td>";
        echo "</tr>";
    ?>
</tbody>