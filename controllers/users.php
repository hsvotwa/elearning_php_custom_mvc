<?php
class UsersController extends BaseController {
    public function __construct () {
    }

    function manage() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        $this->g_can_edit = ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage );
        $mgr = new UserMgr();
        $this->render("manage", $mgr->getRecordPageTitle() );
    }

    function list( $search_text ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $this->g_can_edit = ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage );
        $model = new UserMgr( $search_text );
        $this->g_layout = null;
        $this->g_form_fields = ( new UserMdl() )->getFields();
        $this->g_records = $model->getRecords();
        $this->render("list");
    }
}