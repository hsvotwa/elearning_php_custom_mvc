<?php
if( ! $records ) {
    echo "<thead id=\"th_cont_data\"><tr><td>No records found</td></tr></thead>";
    return;
}
?>
<table class="tbl_cont text-center w-100">
    <tr>
        <td class="tbl_cont_td_2">
            <div class="div_filter" style="text-align:center">
               <h2 style="color: green;">Your application has been submitted. </h2>
               <p>You will be notified once it has been processed. For confirmation, please take a look at the quotation below:</p>
<table class="tbl_cont_data tbl_cont_data_filter w-100" id="record_list">
<tbody id="tb_cont_data w-100">
    <?php
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
</table>
        </td>
    </tr>
</table>