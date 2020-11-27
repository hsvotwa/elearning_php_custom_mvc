<div class="div_center">
    <form>
        <table class="w-100">
            <tr>
                <td style="text-align:center">
                    <font class="font_h1_maroon"><?php echo $page_title; ?></font>
                </td>
            </tr>
            <tr>
                <td style="text-align:center">
                    <?php
                        foreach ( $form_fields as $field ) {
                            echo $field->getFieldHtml();
                        }
                    ?>
                    <input formaction="<?php echo $form_action; ?>" type="submit" value="Log in with Binary City Time" class="button"/> 
                </td>
            </tr>
        </table>
    </form>
</div>