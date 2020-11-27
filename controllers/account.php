<?php
class AccountController extends BaseController {
    public function __construct () {
        $this->g_layout = "account";
    }

    function login() {
        $model = new LoginMdl();
        $this->g_form_action = URL_TIME_AUTH;
        $this->g_form_fields = ( $model )->getFields();
        $this->render( "login", $model->getRecordPageTitle() );
    }

    function logout() {
        UserSessionMdl::clearUserSession();
        $this->login();
    }

    function loginfeedback( $code ) {
        $model = new LoginMdl();
        $redirect_to = "";
        if ( $model->auth( $code, $redirect_to ) ) {
            $data["success"] = true;
            $data["redirect_to"] = $redirect_to;
            header( "Location: $redirect_to" );
            return;
        }
        echo ( new GeneralDisplay() )->deterFeedback( false, $model->getRecordPageTitle() );
    }
}