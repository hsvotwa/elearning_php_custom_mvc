<?php
class AccountController extends BaseController {
    public function __construct () {
        $this->g_layout = "default";
    }

    function login() {
        $model = new LoginMdl();
        $this->g_form_action = WEBROOT . "account/login";
        $this->g_form_fields = $model->getFields();
        $this->render( "login", $model->getRecordPageTitle() );
    }

    function logout() {
        UserSessionMdl::clearUserSession();
        $this->login();
    }

    function loginfeedback() {
        $model = new LoginMdl();
        if( ! $_POST ) {
            $data["success"] = false;
            return;
        }
        $redirect_to = "";
        if ( $model->auth( $_POST["email"], $_POST["password"], $redirect_to ) ) {
            $data["success"] = true;
            $data["redirect_to"] = $redirect_to;
            echo json_encode( $data );
            return;
        }
        $data["success"] = false;
        $data["message"] = "Incorrect username and/or password.";
        echo json_encode( $data );
    }
}