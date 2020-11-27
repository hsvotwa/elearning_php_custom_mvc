<?php
class UserMdl extends BaseMdl {
    public function __construct ( $id = null, $check_profile = true ) {
        $this->g_id = $id;
        $this->g_entity_name = "User";
        $this->g_sql_table = EnumSqlTbl::tbl_user;
        $this->g_retrieve_query = $this->getRetrieveQuery( $check_profile );
        $this->g_fields = $this->g_invalid_fields = $this->g_errors = array ();
        if ( $id ) {
            $this->retrieve ();
        } else {
            $this->g_id = $this->getMySql()->getUuid();
        }
        $this->getFields();
    }

    protected function getRetrieveQuery( $check_profile = true ) {
        return "select u.*, 
                    ifnull(upa.role_type_id, 0) role_type_id
                    from $this->g_sql_table u 
                    inner join tbl_user_profile_access upa on upa.user_uid = u.user_uuid
                where user_uuid = '$this->g_id'
                and upa.soft_del = " . EnumYesNo::no . 
                ( ! $check_profile ? "" : " and profile_uuid = '" . UserSessionMdl::getProfileId() . "'" );
    }

    public function getRecordPageTitle() {
        return ( ! is_null ( $this->g_row ) ? $this->g_entity_name . ': ' . $this->g_row['surname']  . " " .  $this->g_row['name']  : 'New ' . $this->g_entity_name );
    }

    public function hasAccessTo( $check_type_id ) {
        if ( $check_type_id == EnumUserRoleType::none ) {
            return true;
        }
        $uuid = UserSessionMdl::getUuid();
        $query = "select u.*, 
                        ifnull(upa.role_type_id, 0) role_type_id
                        from $this->g_sql_table u 
                        inner join tbl_user_profile_access upa on upa.user_uid = u.user_uuid
                    where user_uuid = '$uuid'
                    and ifnull( upa.confirmation_code, '' ) = ''
                    and upa.soft_del = " . EnumYesNo::no . "
                    and profile_uuid = '" . UserSessionMdl::getProfileId() . "'";
        $result = $this->getMySql()->getQueryResult( $query );
        if ( ! $result || ! $result->num_rows ) {
            return false;
        }
        $current_role_type_id = mysqli_fetch_array( $result )["role_type_id"]; 
        if ( $check_type_id == $current_role_type_id ) {
            return true;
        }
        if ( $check_type_id == EnumUserRoleType::view ) {
            return $current_role_type_id != EnumUserRoleType::none;
        }
        if ( $check_type_id == EnumUserRoleType::manage_student ) {
            return in_array( $current_role_type_id, array( EnumUserRoleType::manage_student, EnumUserRoleType::manage ) );
        }
        if ( $check_type_id == EnumUserRoleType::manage ) {
            return $current_role_type_id == EnumUserRoleType::manage;
        }
        return false;
    }

    public function setSelectedProfile( $profile_uuid ) {
        $query = "update tbl_user
                    set last_selected_prof_uuid = '$profile_uuid',
                    last_modified = now()
                where user_uuid = '" . $this->g_id . "';";
        return  $this->getMySql()->getQueryResult( $query );
    }

    public function updAccess( $role_type_id ) {
        if ( ! ( new ProfileUserMdl())->userExistsInProfile( UserSessionMdl::getProfileId(), $_POST["uid"], $existing ) ) {
            return true;
        }
        $query = "update tbl_user_profile_access
                    set role_type_id = $role_type_id,
                    last_modified = now()
                where user_uid = '" . $this->g_id . "'
                and profile_uuid = '" . UserSessionMdl::getProfileId() . "'
                and soft_del = " . EnumYesNo::no;
        if( $this->getMySql()->getQueryResult( $query ) ) {
            $field = new FieldMdl( 
                "profile_user", "profile_user", "Role type", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row, EnumSqlTbl::tbl_lu_role_type, 2, null, "", 6, true
            );
            $field->g_old_value = $existing["role_type_id"];
            $field->g_new_value = $role_type_id;
            $audit_trail = new AuditTrail(
                UserSessionMdl::getUuid(),
                $existing["uuid"],
                $field
            );
            $audit_trail->trackChange(  $field );
            return true;
        }
        return false;
    }

    public function getFields() {
        if ( $this->g_fields != null ) {
            return $this->g_fields;
        }
        $return = array ();
        $return["name"] = new FieldMdl( 
            "name", "name", "Name", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["surname"] = new FieldMdl( 
            "surname", "surname", "Surname", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["role_type_id"] = new FieldMdl( 
            "role_type_id", "role_type_id", "Role type", true, EnumFieldDataType::_integer, EnumFieldType::_radiobutton, $this->g_sql_table, true, "d-block no_italic", $this->g_row, EnumSqlTbl::tbl_lu_role_type, 2, LookupData::getRoleTypeList()
        );
        $this->g_fields = $return;
        return $this->g_fields;
    }
}