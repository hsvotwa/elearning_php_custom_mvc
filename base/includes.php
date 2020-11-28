<?php
    require_once ( ROOT . "classes/enum.php" );
    require_once ( ROOT . "classes/common.php" );
    $root = ROOT;
    spl_autoload_register( function( $cls_name ) use ( $root ) {
        $values = preg_split('/(?=[A-Z])/', trim( $cls_name ), NULL, PREG_SPLIT_NO_EMPTY );
        if( Common::endsWith( $cls_name, "Mdl" ) ) {
            $dir = "{$root}models";
            array_pop( $values );
        } else if ( Common::endsWith( $cls_name, "Mgr" ) ) {
            $dir = "{$root}model_mgrs";
            array_pop( $values );
        } else if ( Common::endsWith( $cls_name, "Controller" ) ) {
            $dir = "{$root}controllers";
            array_pop( $values );
        } else {
            $dir = "{$root}classes";
        }
        // var_dump($values);
        $file_name = strtolower(  implode( "_", $values ) );
        if ( file_exists( $path = "{$dir}/{$file_name}.php" ) ) {
            require_once( $path );
        }
    });
    require_once ( ROOT . "scripts/constant.php" );
    require_once ( ROOT . "webroot/route_config.php" );
    require_once ( ROOT . "webroot/request.php" );
    require_once ( ROOT . "webroot/dispatcher.php" );