<div style="margin: 15px 5px">
    <a href="<?php echo APP_DOMAIN; ?>students/manage/" class="button">Back</a>
    <a href="#" id="btn_create_unit" class="button <?php echo $form_fields["client_id"]->g_value_object["client_id"] ? "" : "hidden" ?>" onclick="showDialog();">Create units</a>
</div>
<form method="post" action="<?php echo $form_action; ?>" id="frm_main">
    <table class="tbl_cont">
        <tr>
            <td class="tbl_cont_td_2">
                <div id="tab">
                    <ul>
                        <li id="li-tab-gen" class="<?php echo $form_fields["client_id"]->g_value_object["client_id"] ? "" : "tab_pending" ?>">
                            <a href="#tab-gen">General</a>
                        </li>
                        <li id="li-tab-cred" class="<?php echo $form_fields["client_id"]->g_value_object["client_id"] ? "" : "tab_pending" ?>">
                            <a href="#tab-cred">BC Time API Credentials</a>
                        </li>
                        <li id="li-tab-units">
                            <a href="#tab-units" id="tab-link-units">Units</a>
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
                                    echo $form_fields["name"]->getFieldHtml();
                                    ?>
                                </td>
                                <td class="w-50">
                                    <?php
                                    echo $form_fields["tel_no"]->getFieldHtmlLabel();
                                    echo $form_fields["tel_no"]->getFieldHtml();
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
                                    echo $form_fields["client_id"]->getFieldHtml();
                                    ?>
                                </td>
                                <td class="w-50">
                                    <?php
                                    echo $form_fields["client_secret"]->getFieldHtmlLabel();
                                    echo $form_fields["client_secret"]->getFieldHtml();
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50">
                                    <?php
                                    echo $form_fields["encryption_key"]->getFieldHtmlLabel();
                                    echo $form_fields["encryption_key"]->getFieldHtml();
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
<div id="create_units" class="hidden">
</div>
<?php
echo $gen->getJavascriptRef('js/student.js')
?>
<script>
    function showDialog() {
        $('#create_units').load('<?php echo WEBROOT . "student/studentfloor"; ?>',
            function() {
                $("#student_uuid").val( $("#uuid").val() );
                $("#number_of_floors").val(g_floor_count);
                $("#floor_number_type").val(g_floor_number_type);
                $("#unit_number_type").val(g_unit_number_type);
                $("#student_uuid").val($("#uuid").val());
                dialogHandler('Create units', $('#create_units'), getUnitsDialog, null, 300, function() {
                    $("#unit_number_type").val( "" );
                    $("#number_of_floors").val( "" );
                    $("#floor_number_type").val( "" );
                    $("#create_units").dialog( "destroy" );
                    g_floor_count = "";
                    g_floor_number_type = "";
                    g_unit_number_type = "";
                    g_first_floor_field = "";
                    g_floor_unit_data = {};
                }, true, false, false, "Next", "Cancel");
            });
    }
</script>