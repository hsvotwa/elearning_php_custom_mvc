<?php
class UserController extends BaseController {
    public function __construct () {
    }

    function edit( $id ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage ) ) {
            $this->detail( $id );
            return;
        }
        $this->set( array( $id ) );
        $model = new UserMdl( $id );
        if( ! $model->g_row ) {
            ( new ErrorController() )->Error404();
            return;
        }
        $this->g_record_id = $model->g_row["user_uuid"];
        $this->g_form_fields = ( $model )->getFields();
        // if ( ( new ProfileUserMdl() )->userExistsInProfile( UserSessionMdl::getProfileId(), $id, $existing ) ) {
        //     $this->g_other_data = array(
        //         "uuid" => $existing["uuid"]
        //     );
        // }
        $this->render( "edit", $model->getRecordPageTitle() );
    }

    function save() {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::manage ) ) {
            echo ( new GeneralDisplay() )->deterFeedback( false, "", UNAUTHORISED_MESSAGE );
            return;
        }
        $uid = (
            isset( $_POST['uid'] ) && !empty( $_POST['uid'] )
            ? $_POST['uid']
            : null
        );
        $model = new UserMdl( $uid );
        $model->getFields();
        $success = $model->updAccess( $_POST["role_type_id"] );
        echo ( new GeneralDisplay() )->deterFeedback( $success, $model->getRecordPageTitle() );
    }

    function detail( $id ) {
        if( ! ( new UserMdl() )->hasAccessTo( EnumUserRoleType::view ) ) {
            ( new ErrorController() )->Error403();
            return;
        }
        $this->set( array( $id ) );
        $model = new UserMdl( $id );
        if( ! $model->g_row ) {
            ( new ErrorController() )->Error404();
            return;
        }
        $this->g_record_id = $model->g_row["user_uuid"];
        $this->g_form_fields = ( $model )->getFields();
        $this->render( "detail", $model->getRecordPageTitle() );
    }
}