<?php
class StudentMdl extends BaseMdl {
    public function __construct ( $id = null, $check_profile = true ) {
        $this->g_id = $id;
        $this->g_entity_name = "Student";
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
        return "select *
                    from $this->g_sql_table u
                where uuid = '$this->g_id'";
    }

    public function getRecordPageTitle() {
        return ( ! is_null ( $this->g_row ) ? $this->g_entity_name . ': ' . $this->g_row['name']  : 'Register as a ' . $this->g_entity_name );
    }

    public function getFields() {
        if ( $this->g_fields != null ) {
            return $this->g_fields;
        }
        $return = array ();
        $return["name"] = new FieldMdl( 
            "name", "name", "Name", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["tel_no"] = new FieldMdl( 
            "tel_no", "tel_no", "Telephone", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["email"] = new FieldMdl( 
            "email", "email", "Email", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $this->g_fields = $return;
        return $this->g_fields;
    }
}