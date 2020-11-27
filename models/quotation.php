<?php
class studentMdl extends BaseMdl {
    public $g_current_unit_count = null;

    public function __construct ( $id = null ) {
        $this->g_id = $id;
        $this->g_current_unit_count = 0;
        $this->g_entity_name = "student";
        $this->g_sql_table = EnumSqlTbl::tbl_student;
        $this->g_retrieve_query = $this->getRetrieveQuery();
        $this->g_fields = $this->g_invalid_fields = $this->g_errors = array ();
        $this->setAddtionalSqlField();
        if ( $id ) {
            $this->retrieve ();
            $this->getCurrentUnitCount();
        } else {
            $this->g_id = $this->getMySql()->getUuid();
        }
        $this->getFields();
    }

    protected function getRetrieveQuery () {
        if( is_numeric( $this->g_id ) ) {
            return "select * from $this->g_sql_table where id = '$this->g_id'";
        }
        return "select * from $this->g_sql_table where uuid = '$this->g_id'";
    }

    protected function setAddtionalSqlField() {
        $this->g_additional_sql = "profile_uuid = '" . UserSessionMdl::getProfileId() . "'";
    }

    private function getCurrentUnitCount() {
        $query = "select * from tbl_unit
                    where student_uuid = '" . $this->g_id . "'
                    and soft_del = " . EnumYesNo::no . " ;";
        $existing = $this->getMySql()->getQueryResult( $query );
        if ( ! $existing ) {
            $this->g_current_unit_count = 0;
            return true;
        }
        $this->g_current_unit_count = $existing->num_rows;
    }

    public function getRecordPageTitle() {
        return ( ! is_null ( $this->g_row ) ? $this->g_entity_name . ': ' . $this->g_row['name']  : 'New ' . strtolower( $this->g_entity_name ) );
    }

    public function getFields () {
        if ( $this->g_fields != null ) {
            return $this->g_fields;
        }
        $return = array ();
        /*  Field properties:
            $html_name, $mysql_name, $description, $valid, $target_type, $mysql_tbl, $compulsory, $css_class,
            $mysql_ref_tbl = null, $decimal_place_count = 2, $list_source = null, $list_default = '-- please select --'
        */
        $return["name"] = new FieldMdl( 
            "name", "name", "Name", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["tel_no"] = new FieldMdl( 
            "tel_no", "tel_no", "Telephone number", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text telephone_number_field", $this->g_row, null, 2, null, "", 6
        );
        $return["client_id"] = new FieldMdl( 
            "client_id", "client_id", "Client ID", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row, null, 2, null, null, 0, false
        );
        $return["client_secret"] = new FieldMdl( 
            "client_secret", "client_secret", "Client secret", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row, null, 2, null, null, 0, false
        );
        $return["encryption_key"] = new FieldMdl( 
            "encryption_key", "encryption_key", "Encryption key", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row, null, 2, null, null, 0, false
        );
        $this->g_fields = $return;
        return $this->g_fields;
    }
}