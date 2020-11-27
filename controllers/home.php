<?php
class HomeController extends BaseController {
    public function __construct () {
    }

    function welcome() {
        $this->render( "welcome" );
    }
}