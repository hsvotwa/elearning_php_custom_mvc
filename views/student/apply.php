<div style="margin: 15px 5px">
<form method="post" action="<?php echo $form_action; ?>" id="frm_main">
    <table class="tbl_cont">
        <tr>
            <td class="tbl_cont_td_2">
                <div id="tab">
                    <ul>
                        <li id="li-tab-gen">
                            <a href="#tab-gen">Detail & Payment options</a>
                        </li>
                        <li id="li-tab-subjects" style="display:none;">
                            <a href="#tab-subjects" id="tab-link-subjects">Subjects & Study Aids</a>
                        </li>
                        <li id="li-tab-quotation" style="display:none;">
                            <a href="#tab-quotation" id="tab-link-quotation">Quotation</a>
                        </li>
                    </ul>
                    <div id="tab-gen">
                        <input type="hidden" name="uuid" id="uuid" value="<?php echo $record_id; ?>" />
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
                                <td class="w-50">
                                    <?php
                                    echo $form_fields["payment_term_id"]->getFieldHtmlLabel();
                                    echo $form_fields["payment_term_id"]->getFieldHtml();
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="tab-subjects" style="display:none;">
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
                    <div id="tab-quotation" style="display:none;">
                        <div id="quotation_breakdown"></div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="w-50">
                <input type="submit" value="Save" class="button" />
            </td>
            <td class="w-50">
                <input type="submit" value="Submit application" class="button" formaction="<?php APP_DOMAIN . "student/submitapplication"; ?>" />
            </td>
        </tr>
    </table>
</form>
<?php
echo $gen->getJavascriptRef('js/apply.js')
?>