<form method="post" action="<?php echo $form_action; ?>" id="frm_create_units">
  <input type="hidden" name="student_uuid" id="student_uuid" value="<?php echo $record_id; ?>" />
  <div id="error_label"></div>
    <table class="w-100">
        <tr>
            <td class="w-50">
                <?php
                echo $form_fields["number_of_floors"]->getFieldHtmlLabel();
                echo $form_fields["number_of_floors"]->getFieldHtml();
                ?>
            </td>
        </tr>
        <tr>
            <td class="w-50">
                <?php
                echo $form_fields["floor_number_type"]->getFieldHtmlLabel();
                echo $form_fields["floor_number_type"]->getFieldHtml();
                ?>
            </td>
        </tr>
        <tr>
            <td class="w-50">
                <?php
                echo $form_fields["unit_number_type"]->getFieldHtmlLabel();
                echo $form_fields["unit_number_type"]->getFieldHtml();
                ?>
            </td>
        </tr>
    </table>
</form>