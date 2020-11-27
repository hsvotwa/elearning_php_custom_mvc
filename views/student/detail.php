<div style="margin: 15px 5px">
    <a href="<?php echo APP_DOMAIN; ?>students/manage/" class="button">Back</a>
</div>
<form>
    <table class="tbl_cont">
        <tr>
            <td class="tbl_cont_td_2">
                <div id="tab">
                    <ul>
                    <li id="li-tab-gen">
                            <a href="#tab-gen">General</a>
                        </li>
                        <li id="li-tab-cred" class="<?php echo $form_fields["client_id"]->g_value_object["client_id"] ? "" : "tab_pending" ?>">
                            <a href="#tab-cred">BC Time API Credentials</a>
                        </li>
                        <li id="li-tab-units">
                            <a href="#tab-units">Units</a>
                        </li>
                    </ul>
                    <div id="tab-gen">
                        <input type="hidden" name="uuid" id="uuid" value="<?php echo $record_id; ?>" />
                        <input type="submit" class="hidden" />
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
                                    echo $form_fields["tel_no"]->getFieldHtmlLabel();
                                    echo $form_fields["tel_no"]->getFieldHtmlDisplay();
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="tab-cred">
                    <table class="w-100">
                        <tr>
                            <td class="w-50">
                                <?php
                                echo $form_fields["client_id"]->getFieldHtmlLabel();
                                echo $form_fields["client_id"]->getFieldHtmlDisplay();
                                ?>
                            </td>
                            <td class="w-50">
                                <?php
                                echo $form_fields["client_secret"]->getFieldHtmlLabel();
                                echo $form_fields["client_secret"]->getFieldHtmlDisplay();
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50">
                                <?php
                                echo $form_fields["encryption_key"]->getFieldHtmlLabel();
                                echo $form_fields["encryption_key"]->getFieldHtmlDisplay();
                                ?>
                            </td>
                        </tr>
                    </table>
                    </div>
                    <div id="tab-units">
                    </div>
                </div>
            </td>
            <td class="tbl_cont_td_3">
                <div id="div_chg_log"></div>
            </td>
        </tr>
    </table>
</form>
<?php
    echo $gen->getJavascriptRef('js/student.js')
?>