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
        <div class="div_hd_logo"><a href="http://www.binarycity.com.na" target="_blank"><img src="<?php echo $logo_path ?>" id="img_bc_logo" /></a></div>
    </header>
    <div id="div_cont">
        <?php
            echo $content_for_layout;
        ?>
    </div>
</body>
</html>