<?php
if( ! $records ) {
    echo "<thead id=\"th_cont_data\"><tr><td>No records found</td></tr></thead>";
    return;
}
?>
<tbody id="tb_cont_data">
    <?php
        echo '<tr><td><h3>Fees breakdown</h3></td></tr>';
        echo "<tr>";
        echo '<td style="text-align:center">Subject cost: <b>$ ' . Convert::toNum( $records['subject_cost'] ) . '</b></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td style="text-align:center">Study aids cost: <b>$ ' . Convert::toNum( $records['aid_cost'] ) . '</b></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td style="text-align:center">Total cost: <b>$ ' . Convert::toNum( $records['total_cost'] ) . '</b></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td style="text-align:center">Interest percentage: <b>' . Convert::toNum( $records['interest_percent'] ) . '%</b></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td style="text-align:center">Interest amount: <b>$ ' . Convert::toNum( $records['interest_amount'] ) . '</b></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td style="text-align:center">Total due: <b>$ ' . Convert::toNum( $records['total_due'] ) . '</b></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td style="text-align:center">Payment period: <b>' . $records['period'] . '</b></td>';
        echo "</tr>";
        
        echo "<tr>";
        echo '<td style="text-align:center">Monthly payment: <b>$ ' . Convert::toNum( $records['monthly_payment'] ) . '</b></td>';
        echo "</tr>";
    ?>
</tbody>