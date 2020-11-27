<?php
class AccountController extends BaseController {
    public function __construct () {
        $this->g_layout = "account";
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
        $redirect_to = "";
        if ( $model->auth( $_POST["email"], $_POST["password"], $redirect_to ) ) {
            $data["success"] = true;
            $data["redirect_to"] = $redirect_to;
            header( "Location: $redirect_to" );
            return;
        }
        echo ( new GeneralDisplay() )->deterFeedback( false, $model->getRecordPageTitle() );
    }
}