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
        if( ! ( new StudentMgr() )->validIdNum( $_POST['id_no'], $uuid ) ) {
            $data["success"] = false;
            $data["message"] = "The ID number you provided is already registered for another student.";
            echo json_encode( $data );
            return;
        }
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
        $complex_uuid = ( new UnitMdl( $unit_uuid ) )->g_row["complex_uuid"];
        $model = new DeviceMgr( $unit_uuid, $complex_uuid );
        $this->g_layout = null;
        $error_message = "";
        $this->g_records = $model->getRecords();
        $this->render("devicelist");
    }
}