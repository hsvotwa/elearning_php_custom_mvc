<div style="margin: 15px 5px">
<form method="post" action="<?php echo $form_action; ?>" id="frm_main">
    <table class="tbl_cont">
        <tr>
            <td class="tbl_cont_td_2">
                <div id="tab">
                    <ul>
                        <li id="li-tab-gen">
                            <a href="#tab-gen">Your information</a>
                        </li>
                        <li id="li-tab-subjects">
                            <a href="#tab-subjects" id="tab-link-subjects">Subjects & Study aids</a>
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
                            <tr>
                                <td class="w-50">
                                    <?php
                                    echo $form_fields["email"]->getFieldHtmlLabel();
                                    echo $form_fields["email"]->getFieldHtml();
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="tab-subjects">
                        <table class="w-100">
                            <tr>
                                <td class="w-50 align-top">
                                    <table class="w-100">
                                        <tr>
                                            <td>
                                                <input id="subject" placeholder="Search and select subject(s)..." type="text" class="w-100 text ui-autocomplete-input"/> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="subject_list"></td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="w-50 align-top">
                                    <table class="w-100">
                                        <tr>
                                            <td>
                                                <input id="aid" placeholder="Search and select study aid(s)..." type="text" class="w-100 text ui-autocomplete-input"/> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td id="aid_list"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="w-50">
                <input type="submit" value="Submit" class="button" />
            </td>
        </tr>
    </table>
</form>
<?php
echo $gen->getJavascriptRef('js/apply.js')
?>