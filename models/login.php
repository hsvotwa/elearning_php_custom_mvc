<?php
class LoginMdl extends BaseMdl {
    public function __construct () {
        $this->g_entity_name = "Login";
        $this->g_fields = $this->g_invalid_fields = $this->g_errors = array ();
        $this->getFields();
    }

    public function getRecordPageTitle() {
        return "Login";
    }

    public function auth( $auth_code, &$redirect_to ) {
        parse_str( parse_url( $auth_code )['query'], $query );
        $auth_code = $query['code'];
        if( ! $auth_code ) {
            $redirect_to = WEBROOT . "account/login";
            return true;
        }
        $redirect_to = "";
        $user_data = ( new TimeIntegration(null, OAuth2GrantType::auth_code, $auth_code ) )->authenticate();
        if( ! $user_data || !is_array( $user_data ) ) {
            $redirect_to = WEBROOT . "account/login";
            return false;
        }
        if ( ! $this->save( $user_data ) ) {
            $redirect_to = WEBROOT . "account/login";
            return false;
        }
        $prev_selected_profile_uuid = ( new UserMdl( $user_data["user_uid"], false ) )->g_row["last_selected_prof_uuid"];
        $profile_row = null;
        if( $prev_selected_profile_uuid ) {
            $profile_row = ( new ProfileMdl( $prev_selected_profile_uuid ) )->g_row;
            if( $profile_row && ! ( new ProfileUserMdl() )->userExistsInProfile( $prev_selected_profile_uuid, $user_data["user_uid"], $existing )) {
                $profile_row = null;
            }
        }
        if( ! $profile_row ) {
            $user_profiles = $this->getProfiles( $user_data["user_uid"] );
            if ( $user_profiles && $user_profiles->num_rows ) {
                $profile_row = mysqli_fetch_assoc( $user_profiles );
            }
        }
        UserSessionMdl::setUserSession(
            array(
                "user_uid" => $user_data["user_uid"], 
                "user_name" => $user_data["name"], 
                "user_surname" => $user_data["surname"], 
                "selected_profile_id" => $profile_row ? $profile_row["uuid"] : null, 
                "selected_profile_name" =>  $profile_row ? $profile_row["name"] : null
                )
            );
        if ( ! $profile_row ) {
            $redirect_to = WEBROOT . "profile/forcenew";
            return true;
        }
        $redirect_to = WEBROOT . "home/welcome";
        return true;
    }

    private function get( $user_uid ) {
        $query = "select * from tbl_user where user_uuid = '$user_uid';";
        $records = $this->getMySql()->getQueryResult( $query );
        return $records && $records->num_rows;
    }

    public function save( $user_data ) {
        $exists = $this->get( $user_data["user_uid"] );
        $query = $exists ? "update tbl_user set " : "insert into tbl_user set uuid = uuid(), ";
        $query .= " name = '" . $user_data["name"] . "',";
        $query .= " surname = '" . $user_data["surname"] . "',";
        $query .= " status_id = '" . $user_data["status_id"] . "',";
        $query .= " last_modified = now() ";
        $query .= $exists ? " where " : ", created = now(), ";
        $query .= " user_uuid =  '" . $user_data["user_uid"] . "' ";
        return $this->getMySql()->getQueryResult( $query );
    }

    private function getProfiles( $user_uid ) {
        $query = "select * 
                    from tbl_profile 
                    where uuid in (
                        select 
                        profile_uuid 
                        from tbl_user_profile_access 
                        where user_uid = '$user_uid' 
                        and soft_del = " . EnumYesNo::no . "
                        and ifnull(confirmation_code, '') = ''
                    );";
        return $this->getMySql()->getQueryResult( $query );
    }

    public function getPendingProfiles( $user_uid ) {
        $query = "select * 
                    from tbl_profile 
                    where uuid in (
                        select 
                        profile_uuid 
                        from tbl_user_profile_access 
                        where user_uid = '$user_uid' 
                        and soft_del = " . EnumYesNo::no . "
                        and ifnull(confirmation_code, '') != ''
                    );";
        $user_profiles =  $this->getMySql()->getQueryResult( $query );
        return $user_profiles && $user_profiles->num_rows;
    }

    public function getFields () {
        if ( $this->g_fields != null ) {
            return $this->g_fields;
        }
        $return = array ();
        $return["email"] = new FieldMdl( 
            "email", "email", "", true, EnumFieldDataType::email, EnumFieldType::_hidden, $this->g_sql_table, true, "text", $other_values
        );
        $return["password"] = new FieldMdl( 
            "password", "password", "", true, EnumFieldDataType::_string, EnumFieldType::_hidden, $this->g_sql_table, true, "text", $other_values
        );
        $this->g_fields = $return;
        return $this->g_fields;
    }
}