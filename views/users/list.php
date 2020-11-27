<?php
if( ! $records || ! $records->num_rows ) {
    echo "<thead id=\"th_cont_data\"><tr><td>No records found</td></tr></thead>";
    return;
}
?>
<thead id="th_cont_data">
<tr>
    <td class="head_td" width="400">
        <?php 
            echo $form_fields["name"]->getFieldHtmlLabel( /*is_form=*/ false );
        ?>
    </td>
    <td class="head_td" width="100">
    </td>
</tr>
</thead>
<tbody id="tb_cont_data" class="tbl_cont_data">
    <?php
    foreach ( $records as $record ) {
        $action = UserSessionMdl::getUuid() != $record["user_uuid"] && $can_edit ? "edit" : "detail";
        echo $record["user_uuid"] . "</td>";
        echo "<td width=\"400\"><a class='url' href='" . WEBROOT . "user/$action/" . $record["user_uuid"] . "' >" . $record['surname'] . " " . $record['name'] . "</a>" .
        ( $record["confirmation_code"] ? " (pending confirmation)" : "") . "</td>";
        if( $record["user_uuid"] != UserSessionMdl::getUuid() && $can_edit ) {
            echo "<td width=\"100\"><a href=\"#\" class=\"action_link\" onclick='removeUser(\"" . $record["user_uuid"] . "\")'>revoke access</a></td>";
        } else {
            echo "<td>&nbsp;</td>";
        }
        echo "</tr>";
    }
    ?>
</tbody>