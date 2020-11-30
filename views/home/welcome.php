<div class="div_center">
        <table class="w-100">
            <tr>
                <td class="text-center">
                    <h1 style="font-size: 30px;" class="font_h1_maroon">Welcome to <?php echo APP_NAME; ?></font>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                   <img src="<?php echo WEBROOT; ?>images/logo.png"/>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <h1>Modern Subjects</h1>
                    <h4 style="color: #800080">We offer modern and relevant subjects. </h4></br>
                    <p>Please take a look at the subjects on offer in 2021.</p>
                    <table class="tbl_cont">
                        <tr>
                            <td class="tbl_cont_td_2">
                                <table class="tbl_cont_data w-100" id="subjects">

                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                <br><br>
                    <h1>Cutting-edge Technology</h1>
                    <h4 style="color: #800080">We use cutting-edge technology to aid our learners with their studies. </h4></br>
                    <p>Please take a look at the items on offer in 2021.</p>
                    <table class="tbl_cont">
                        <tr>
                            <td class="tbl_cont_td_2">
                                <table class="tbl_cont_data w-100" id="aids">

                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
</div>
<script>
    $(function() {
        $('#subjects').load('<?php echo WEBROOT . "subjects/list"; ?>',
            function() {
        });
        $('#aids').load('<?php echo WEBROOT . "aids/list"; ?>',
            function() {
        });
    });
</script>