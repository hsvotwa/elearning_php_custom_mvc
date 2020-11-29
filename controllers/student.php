<?php
class studentController extends BaseController {
    public function __construct () {
    }

    function apply() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        $model = new studentMdl();
        $this->g_form_fields = $model->getFields();
        $this->g_record_id = $model->g_id;
        $this->g_form_action = WEBROOT . "student/saveapplication";
        $this->render( "apply", $model->getRecordPageTitle() );
    }

    function saveapplication() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $uuid = (
            isset( $_POST['uuid'] ) && !empty( $_POST['uuid'] )
            ? $_POST['uuid']
            : null
        );
        $model = new StudentMdl( $uuid );
        $model->getFields();
        $error_message = "";
        $mgr = new StudentMgr();
        if( ! $mgr->validEmail( $_POST['email'], $uuid ) ) {
            $data["success"] = false;
            $data["message"] = "The email address you provided is already registered for another student.";
            echo json_encode( $data );
            return;
        }
        $student_no = $mgr->getNextStudentNumber();
        $model->g_additional_sql = " student_no = '$student_no', status_id = '" . EnumStudentStatus::applied . "'";
        $success = $model->set();
        if ( $error_message ) {
            $model->g_errors[] = $error_message;
        }
        echo ( new GeneralDisplay() )->deterFeedback( $success, $model->getRecordPageTitle(), implode( ",", $model->g_errors ) );
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

    function getaids( $student_uuid ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $this->g_can_edit = true;
        $model = new StudentMdl( $student_uuid );
        $this->g_layout = null;
        $error_message = "";
        $this->g_records = $model->getAids();
        $this->render("aidlist");
    }

    function getsubjects( $student_uuid ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::none ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $this->g_can_edit = true;
        $model = new StudentMdl( $student_uuid );
        $this->g_layout = null;
        $error_message = "";
        $this->g_records = $model->getSubjects();
        $this->render("subjectlist");
    }

    function linksubject() {
        $model = new StudentMdl();
        $model->getFields();
        $error = "";
        $success = $model->linkSubject( $error );
        echo ( new GeneralDisplay() )->deterFeedback( $success, "", $error );
    }

    function removesubject() {
        $model = new StudentMdl();
        $model->getFields();
        $message = "";
        $success = $model->removeSubject( $message );
        echo ( new GeneralDisplay() )->deterFeedback( $success, "", $message );
    }

    function linkaid() {
        $model = new StudentMdl();
        $model->getFields();
        $error = "";
        $success = $model->linkAid( $error );
        echo ( new GeneralDisplay() )->deterFeedback( $success, "", $error );
    }

    function removeaid() {
        $model = new StudentMdl();
        $model->getFields();
        $message = "";
        $success = $model->removeAid( $message );
        echo ( new GeneralDisplay() )->deterFeedback( $success, "", $message );
    }
}