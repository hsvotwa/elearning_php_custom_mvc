<?php
class AidsController extends BaseController {
    public function __construct () {
    }

    function manage() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        $this->g_can_edit = ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none );
        $mgr = new AidMgr();
        $this->render( "manage", $mgr->getRecordPageTitle() );
    }

    function list( $search_text = "" ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $this->g_can_edit = ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none );
        $model = new AidMgr( $search_text );
        $this->g_layout = null;
        $this->g_form_fields = null;//( new AidMdl() )->getFields();
        $this->g_records = $model->getRecords();
        $this->render( "list" );
    }

    function unlinkedlist( $search_text, $student_uuid ) {
        $this->g_layout = null;
        $this->g_records = LookupData::getUnlinkedStudyAidList( $search_text, $student_uuid );
        $this->render("unlinkedlist");
    }
}