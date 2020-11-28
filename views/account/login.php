<form method='post' action='<?php echo $form_action; ?>' id="frm_main">
    <table class="tbl_cont">
        <tr>
            <td class="tbl_cont_td_2">
                <div id="tab-gen">
                    <table class="w-100">
                        <tr>
                            <td class="w-50">
                                <?php
                                    echo $form_fields["email"]->getFieldHtmlLabel();
                                    echo $form_fields["email"]->getFieldHtml();
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50">
                                <?php
                                    echo $form_fields["password"]->getFieldHtmlLabel();
                                    echo $form_fields["password"]->getFieldHtml();
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50">
                                <input type="submit" value="Submit" class="button" />
                            </td>
                        </tr>
                    </table>
                </div>
                </div>
            </td>
        </tr>
    </table>
    </form>
    <?php
    echo $gen->getJavascriptRef('js/login.js')
    ?>