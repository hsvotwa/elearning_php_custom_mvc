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

    public function auth( $email, $password, &$redirect_to ) {
        $redirect_to = "";
        $query = "select u.*, ut.name as user_type 
                    from tbl_user u 
                    inner join tbl_lu_user_type ut on ut.enum_id = u.user_type_id 
                    where email = '$email' and password = '" . md5( $password ) . "';";
        $records = $this->getMySql()->getQueryResult( $query );
        if( ! $records || ! $records->num_rows ) {
            $redirect_to = WEBROOT . "account/login";
            return false;
        }
        $user_data = mysqli_fetch_array( $records );
        if( ! $user_data || !is_array( $user_data ) ) {
            $redirect_to = WEBROOT . "account/login";
            return false;
        }
        UserSessionMdl::setUserSession(
            array(
                "uuid" => $user_data["uuid"], 
                "user_name" => $user_data["name"], 
                "user_surname" => $user_data["surname"], 
                "user_type_id" => $user_data["user_type_id"], 
                "user_type" =>  $user_data["user_type"]
                )
            );
        $redirect_to = WEBROOT . "home/welcome";
        return true;
    }

    private function get( $user_uuid ) {
        $query = "select * from tbl_user where uuid = '$user_uuid';";
        $records = $this->getMySql()->getQueryResult( $query );
        return $records && $records->num_rows;
    }

    public function save( $record_data ) {
        $exists = $this->get( $record_data["uuid"] );
        $query = $exists ? "update tbl_user set " : "insert into tbl_user set uuid = uuid(), ";
        $query .= " name = '" . $record_data["name"] . "',";
        $query .= " surname = '" . $record_data["surname"] . "',";
        $query .= " password = '" . md5( $record_data["password"] ) . "',";
        $query .= " status_id = '" . $record_data["status_id"] . "',";
        $query .= " email = '" . $record_data["email"] . "',";
        $query .= " student_uuid = '" . $record_data["student_uuid"] . "',";
        $query .= " last_modified = now() ";
        $query .= $exists ? " where " : ", created = now(), ";
        $query .= " uuid =  '" . $record_data["uuid"] . "' ";
        return $this->getMySql()->getQueryResult( $query );
    }

    public function getFields () {
        if ( $this->g_fields != null ) {
            return $this->g_fields;
        }
        // $html_name, $mysql_name, $description, $valid,
        // $target_data_type, $target_field_type, $mysql_tbl, $compulsory, $css_class,
        // $value_object, $mysql_ref_tbl = null, $decimal_place_count = 2,  
        // $list_source = null, $list_default = '-- please select --', $min_length = 0
        $return = array ();
        $other_values = null;
        $return["email"] = new FieldMdl( 
            "email", "email", "Email", true, EnumFieldDataType::_string, EnumFieldType::_email, $this->g_sql_table, true, "text", $other_values
        );
        $return["password"] = new FieldMdl( 
            "password", "password", "Password", true, EnumFieldDataType::_string, EnumFieldType::_password, $this->g_sql_table, true, "text", $other_values
        );
        $this->g_fields = $return;
        return $this->g_fields;
    }
}