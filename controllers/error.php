<?php
class ErrorController extends BaseController {
    public function __construct () {
    }

    function Error404() {
        $this->render( "404" );
    }

    function Error403() {
        $this->render( "403" );
    }
}