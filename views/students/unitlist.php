<?php
if( ! $records || ! $records->num_rows ) {
    echo "<thead id=\"th_cont_data\"><tr><td>No records found</td></tr></thead>";
    return;
}
?>
<thead id="th_cont_data">
    <tr>
        <td class="head_td" width="100">
            <?php
                echo $form_fields["number"]->getFieldHtmlLabel( /*$is_form = */ false );
            ?>
        </td>
        <td class="head_td text-center" width="200">
            <?php
                echo $form_fields["no_of_rooms"]->getFieldHtmlLabel( /*$is_form = */ false );
            ?>
        </td>
        <td class="head_td text-center" width="200">
            <?php
                echo $form_fields["no_of_students"]->getFieldHtmlLabel( /*$is_form = */ false );
            ?>
        </td>
        <td class="head_td" width="100">
        </td>
    </tr>
</thead>
<tbody id="tb_cont_data" class="tbl_cont_data">
    <?php
    $action = $can_edit ? "edit" : "detail";
    foreach ( $records as $record ) {
        echo "<tr>";
        echo "<td width=\"100\"><a class='url' href='" . WEBROOT . "unit/$action/" . $record["id"] . "' >" . $record['number'] . "</td>";
        echo "<td class=\"text-center\" width=\"200\">" . $record['no_of_rooms'] . "</td>";
        echo "<td class=\"text-center\" width=\"200\">" . $record['no_of_students'] . "</td>";
        if( $can_edit ) {
            if ( UnitMdl::hasstudent( $record["uuid"] ) ) {
                echo "<td width=\"200\">Has student(s)</td>";
            } else {
                echo "<td width=\"200\"><a href=\"#\" class=\"action_link\" onclick='removeUnit(\"" . $record["uuid"] . "\")'>remove</a></td>";
            }
        } else {
            echo "<td><td>";
        }
        echo "</tr>";
    }
    ?>
</tbody>