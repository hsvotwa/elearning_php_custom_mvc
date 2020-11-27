<?php
    class Request {
        public $url;

        public function __construct() {
            $this->url = urldecode( $_SERVER["REQUEST_URI"] );
        }
    }
?>