<?php
class SqlController extends BaseController {
    public function __construct () {
        $this->g_layout = null;
    }

    function update() {
        $this->render("update");
    }
}