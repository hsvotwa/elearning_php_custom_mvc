<form method="post" action="<?php echo $form_action; ?>" id="frm_create_units">
  <input type="hidden" name="student_uuid" id="student_uuid" value="<?php echo $record_id; ?>" />
  <div id="error_label"></div>
  <table class="w-100">
      <?php
        $floors = $other_data["floors"];
        $unit_number_type = $other_data["unit_number_type"];
        $floor_number_type = $other_data["floor_number_type"];
        $floor = null;
        $first_field = null;
        foreach( $floors as $floor ) {
            echo '<tr>
            <td class="w-50">';
            $field = new FieldMdl( 
                $floor, $floor, "Floor " . $floor, true, EnumFieldDataType::_integer, EnumFieldType::_integer, "", true, "text floor_unit_count", null
            );
            echo $field->getFieldHtmlLabel();
            echo $field->getFieldHtml();
            echo '      </td>
            </tr>';
            ! $first_field && $first_field = $floor;
        }
        ?>
        <input type="hidden" name="floor_count" id="floor_count" value="<?php echo count ( $floors ); ?>" />
        <input type="hidden" name="first_field" id="first_field" value="<?php echo $first_field; ?>" />
        <input type="hidden" name="floor_number_type" id="floor_number_type" value="<?php echo $floor_number_type ; ?>" />
        <input type="hidden" name="unit_number_type" id="unit_number_type" value="<?php echo $unit_number_type; ?>" />
    </table>
</form>