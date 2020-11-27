<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        echo $page_html_title;
        echo $css_import;
        echo $javascript_import;
    ?>
</head>
<body>
    <header class="nav-hd-main">
        <div class="nav-div-prog">
            <img src="<?php echo $loader_path ?>" />
        </div>
        <a href="#" class="url_app_title"><?php echo $app_name ?></a>
        <?php 
            echo $navigation 
        ?>
        <div class="div_hd_corner">
            <font class="font_white">&nbsp; <?php echo $user_detail ?></font>
        </div>
        <div class="div_hd_logo"><a href="http://www.binarycity.com.na" target="_blank"><img src="<?php echo $logo_path ?>" id="img_bc_logo" /></a></div>
    </header>
    <div id="div_title">
        <table class="tbl_title_nav_vert">
            <tr>
                <font class="font_h2" id="main_title">
                    <?php
                        echo $page_title;
                    ?>
                </font>
            </tr>
        </table>
    </div>
    <div id="div_cont">
        <?php
            echo $content_for_layout;
        ?>
    </div>
</body>
</html>