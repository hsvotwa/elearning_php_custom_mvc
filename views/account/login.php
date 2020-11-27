<div style="margin: 15px 5px">
    <input type="submit" value="Submit" class="button" />
</div>
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
                                echo $form_fields["email"]->getFieldHtmlDisplay();
                                ?>
                            </td>
                            <td class="w-50">
                                <?php
                                echo $form_fields["password"]->getFieldHtmlLabel();
                                echo $form_fields["password"]->getFieldHtmlDisplay();
                                ?>
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