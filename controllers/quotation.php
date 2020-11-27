<?php
class studentController extends BaseController {
    public function __construct () {
    }

    function create() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage_student ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        $model = new studentMdl();
        $this->g_form_fields = $model->getFields();
        $this->g_record_id = $model->g_id;
        $this->g_form_action = WEBROOT . "student/save";
        $this->render( "edit", $model->getRecordPageTitle() );
    }

    function edit( $id ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage_student ) ) {
            $this->detail( $id );
            return;
        }
        $this->set( array( $id ) );
        $model = new studentMdl( $id );
        if( ! $model->g_row ) {
            ( new ErrorController() )->Error404();
            return;
        }
        $this->g_record_id = $model->g_row["uuid"];
        $this->g_form_fields = ( $model )->getFields();
        $this->render( "edit", $model->getRecordPageTitle() );
    }

    function detail( $id ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        $this->set( array( $id ) );
        $model = new studentMdl( $id );
        if( ! $model->g_row ) {
            ( new ErrorController() )->Error404();
            return;
        }
        $this->g_record_id = $model->g_row["uuid"];
        $this->g_form_fields = ( $model )->getFields();
        $this->render( "detail", $model->getRecordPageTitle() );
    }

    function save() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage_student ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $uuid = (
            isset( $_POST['uuid'] ) && !empty( $_POST['uuid'] )
            ? $_POST['uuid']
            : null
        );
        $model = new studentMdl( $uuid );
        $model->getFields();
        $error_message = "";
        if( ! ( new studentMgr() )->validIdNum( $_POST['id_no'], $uuid ) ) {
            $data["success"] = false;
            $data["message"] = "The ID number you provided is already registered for another student.";
            echo json_encode( $data );
            return;
        }
        $success = $model->set() && $model->pushToBCTime( $error_message );
        if ( $error_message ) {
            $model->g_errors[] = $error_message;
        }
        echo ( new GeneralDisplay() )->deterFeedback( $success, $model->getRecordPageTitle(), implode( ",", $model->g_errors ) );
    }

    function createdocument() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage_student ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $model = new studentDocumentMdl();
        $this->g_form_fields = $model->getFields();
        $this->g_record_id = $model->g_id;
        $this->g_layout = null;
        $this->g_form_action = WEBROOT . "student/savedocument";
        $this->render( "studentdocument", $model->getRecordPageTitle() );
    }

    function savedocument() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage_student ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $model = new studentDocumentMdl();
        $model->getFields();
        $error = "";
        $success = $model->saveAll( $error );
        echo ( new GeneralDisplay() )->deterFeedback( $success, "", $error );
    }

    function removedocument() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage_student ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $model = new studentDocumentMdl();
        $model->getFields();
        $success = $model->remove();
        echo ( new GeneralDisplay() )->deterFeedback( $success, "" );
    }
}