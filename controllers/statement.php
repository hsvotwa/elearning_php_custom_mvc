<?php
class studentsController extends BaseController {
    public function __construct () {
    }

    function manage() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        $this->g_can_edit = ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none );
        $mgr = new studentMgr();
        $this->render( "manage", $mgr->getRecordPageTitle() );
    }

    function list( $search_text ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $this->g_can_edit = ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none );
        $model = new studentMgr( "", $search_text );
        $this->g_layout = null;
        $this->g_form_fields = ( new studentMdl() )->getFields();
        $this->g_records = $model->getRecords();
        $this->render("list");
    }

    function documentlist( $search_text ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $this->g_can_edit = ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none );
        $model = new studentDocumentMgr( $search_text );
        $this->g_layout = null;
        $this->g_form_fields = ( new studentDocumentMdl() )->getFields();
        $this->g_records = $model->getRecords();
        $this->render("documentlist");
    }

    function unlinkedlist( $search_text ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $this->g_layout = null;
        $this->g_records = LookupData::getUnlinkedstudentList( $search_text );
        $this->render("unlinkedlist");
    }
}