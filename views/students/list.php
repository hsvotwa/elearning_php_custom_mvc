<?php
if( ! $records || ! $records->num_rows ) {
    echo "<thead id=\"th_cont_data\"><tr><td>No records found</td></tr></thead>";
    return;
}
?>
<thead id="th_cont_data">
<tr>
    <td class="head_td" width="200">
        <?php 
            echo $form_fields["name"]->getFieldHtmlLabel( /*is_form=*/ false );
        ?>
    </td>
    <td class="head_td" width="200">
        <?php 
            echo $form_fields["tel_no"]->getFieldHtmlLabel( /*is_form=*/ false );
        ?>
    </td>
</tr>
</thead>
<tbody id="tb_cont_data">
    <?php
    $action = $can_edit ? "edit" : "detail";
    foreach ( $records as $record ) {
        echo "<tr>";
        echo "<td width=\"200\"><a class='url' href='" . WEBROOT . "student/$action/" . $record["id"] . "' >" . $record['name'] . "</td>";
        echo "<td width=\"200\">" . $record['tel_no'] . "</td>";
        echo "</tr>";
    }
    ?>
</tbody>