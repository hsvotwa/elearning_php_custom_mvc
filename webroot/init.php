<?php
    define('WEBROOT', str_replace( "webroot/init.php", "", $_SERVER["SCRIPT_NAME"] ) );
    define('ROOT', str_replace( "webroot/init.php", "", $_SERVER["SCRIPT_FILENAME"] ) );
    require_once ( ROOT . "base/includes.php" );
    $dispatch = new Dispatcher();
    $dispatch->dispatch();
?>