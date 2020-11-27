<table class="tbl_cont">
    <tr>
        <td class="tbl_cont_td_2">
            <div class="div_filter">
                <?php 
                    if( $can_edit ) {
                        echo '<a href="#" onclick="showDialog();" class="button">Invite</a>';
                    }
                ?>
                <input type="search" name="search" id="search" class="text" placeholder="Search user(s)..." />&nbsp;
                <a href="#" class="url" id="refresh_link">refresh</a>
            </div>
            <table class="tbl_cont_data tbl_cont_data_filter" id="record_list">

            </table>
        </td>
    </tr>
</table>
<div id="link_user" class="hidden">
</div>
<?php
    echo $gen->getJavascriptRef('js/users.js');
?>
<script>
    function showDialog() {
        $('#link_user').load('<?php echo WEBROOT . "profile/inviteuser"; ?>',
            function() {
                dialogHandler('Invite user', $('#link_user'), linkUsers, null, 350, null, true, false, false);
            });
        }
</script>