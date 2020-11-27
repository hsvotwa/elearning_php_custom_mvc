<div style="margin: 15px 5px">
    <a href="<?php echo APP_DOMAIN; ?>users/manage/" class="button">Back</a>
</div>
<form method='post' action='<?php echo $form_action; ?>' id="frm_main">
    <table class="tbl_cont">
        <tr>
            <td class="tbl_cont_td_2">
                <div id="tab-gen">
                    <input type="hidden" name="uid" id="uid" value="<?php echo $record_id; ?>" />
                    <input type="hidden" name="uuid" id="uuid" value="<?php echo $other_data["uuid"]; ?>" />
                    <table class="w-100">
                        <tr>
                            <td class="w-50">
                                <?php
                                echo $form_fields["name"]->getFieldHtmlLabel();
                                echo $form_fields["name"]->getFieldHtmlDisplay();
                                ?>
                            </td>
                            <td class="w-50">
                                <?php
                                echo $form_fields["surname"]->getFieldHtmlLabel();
                                echo $form_fields["surname"]->getFieldHtmlDisplay();
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50">
                                <?php
                                echo $form_fields["role_type_id"]->getFieldHtmlLabel();
                                echo $form_fields["role_type_id"]->getFieldHtml();
                                ?>
                            </td>
                        </tr>
                    </table>
                    <tr>
                        <td class="w-50">
                           <input type="submit" value="Submit" />
                        </td>
                    </tr>
                </table>
                </div>
                </div>
            </td>
            <td class="tbl_cont_td_3">
                <div id="div_chg_log">
                    No changes found
                </div>
            </td>
        </tr>
    </table>
    </form>
    <?php
    echo $gen->getJavascriptRef('js/user.js')
    ?>