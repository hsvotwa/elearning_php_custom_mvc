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
    <td class="head_td" width="200">
        <?php 
            echo $form_fields["email"]->getFieldHtmlLabel( /*is_form=*/ false );
        ?>
    </td>
    <td class="head_td" width="200">
        <?php 
            echo $form_fields["status_id"]->getFieldHtmlLabel( /*is_form=*/ false );
        ?>
    </td>
    <td></td>
</tr>
</thead>
<tbody id="tb_cont_data">
    <?php
    $action = $can_edit ? "edit" : "detail";
    foreach ( $records as $record ) {
        echo "<tr>";
        echo "<td width=\"200\">" . $record['name'] . "</td>";
        echo "<td width=\"200\">" . $record['tel_no'] . "</td>";
        echo "<td width=\"200\">" . $record['email'] . "</td>";
        echo "<td width=\"200\">" . $record['status'] . "</td>";
        if( $record['status_id'] == EnumStudentStatus::applied ) {
            echo "<td width=\"300\"><a href=\"#\" class=\"action_link\" onclick='approve(\"" . $record["uuid"] . "\")'>approve</a> ";
            echo "<a href=\"#\" class=\"action_link\" onclick='decline(\"" . $record["uuid"] . "\")'>decline</a></td>";
        } else {
            echo "<td></td>";
        }
        echo "</tr>";
    }
    ?>
</tbody>