<?php
class FieldMdl {
   public   $g_value_object = null,
            $g_html_name = null,
            $g_mysql_name = null,
            $g_description = null,
            $g_valid = null,
            $g_target_field_type = null,
            $g_target_data_type = null,
            $g_mysql_tbl = null,
            $g_mysql_ref_tbl = null,
            $g_compulsory = null,
            $g_css_class = null,
            $g_decimal_place_count = null,
            $g_list_source = null,
            $g_list_default = null,
            $g_min_length = null,
            $g_audit_value = null,
            $g_old_value = null,
            $g_new_value = null,
            $g_single_value_audit = false,
            $g_ref_display_col = false;

    private $g_gen = null;

    public function __construct ( $html_name, $mysql_name, $description, $valid,
                                $target_data_type, $target_field_type, $mysql_tbl, $compulsory, $css_class,
                                $value_object, $mysql_ref_tbl = null, $decimal_place_count = 2,  
                                $list_source = null, $list_default = '-- please select --', $min_length = 0, $audit_value = true ) {
        $this->g_html_name = $html_name;
        $this->g_mysql_name = $mysql_name;
        $this->g_description = $description;
        $this->g_valid = $valid;
        $this->g_target_data_type = $target_data_type;
        $this->g_target_field_type = $target_field_type;
        $this->g_mysql_tbl = $mysql_tbl;
        $this->g_mysql_ref_tbl = $mysql_ref_tbl;
        $this->g_compulsory = $compulsory;
        $this->g_css_class = $css_class;
        $this->g_decimal_place_count = $decimal_place_count;
        $this->g_list_source = $list_source;
        $this->g_list_default = $list_default;
        $this->g_value_object = $value_object;
        $this->g_min_length = $min_length;
        $this->g_audit_value = $audit_value;
    }

    public function getGen() {
        if ( is_null ( $this->g_gen ) ) {
            $this->g_gen = new GeneralDisplay();
        }
        return $this->g_gen;
    }

    public function getFieldHtml() {
        return $this->getGen()->deterFieldHtml( $this );
    }

    public function getFieldHtmlDisplay() {
        return $this->getGen()->getHtmlDisplay( $this->g_value_object, $this->g_mysql_name, $this->g_mysql_ref_tbl, $this->deterRefCol() );
    }

    private function deterRefCol() {
        if( ! $this->g_mysql_ref_tbl ) {
            return "";
        }
        switch( $this->g_mysql_ref_tbl ) {
            case EnumSqlTbl::tbl_lu_role_access_type:
            case EnumSqlTbl::tbl_lu_document_type:
            case EnumSqlTbl::tbl_lu_yes_no:
            case EnumSqlTbl::tbl_lu_role_type:
            case EnumSqlTbl::tbl_lu_numbering_type:
            case EnumSqlTbl::tbl_lu_nav:
            case EnumSqlTbl::tbl_lu_nav_role_access:
            case EnumSqlTbl::tbl_audit_trail:
                return "enum_id";
            case EnumSqlTbl::tbl_user:
                return "user_uuid";
        } 
        return "uuid";
    }

    public static function deterRefDisplayCol( $table_name ) {
        if( ! $table_name ) {
            return "";
        }
        switch( $table_name ) {
            case EnumSqlTbl::tbl_lu_role_access_type:
            case EnumSqlTbl::tbl_lu_document_type:
            case EnumSqlTbl::tbl_lu_yes_no:
            case EnumSqlTbl::tbl_lu_role_type:
            case EnumSqlTbl::tbl_lu_numbering_type:
            case EnumSqlTbl::tbl_lu_nav:
            case EnumSqlTbl::tbl_lu_nav_role_access:
                return "name";
            case EnumSqlTbl::tbl_student:
            case EnumSqlTbl::tbl_user:
                return "concat(surname, ' ', name)";
        } 
        return "name";
    }

    public function getFieldHtmlLabel( $is_form = true ) {
        return $this->getGen()->getHtmlLabel( $this->g_description, $is_form );
    }

    public function validate() {
        if ( ! $this->g_html_name ) {
            return false;
        }
        $return = true;
        if ( $_POST ) {
            if ( isset ( $_POST[ $this->g_html_name ] ) ) {
                if ( $this->g_compulsory ) {
                    $field_value = trim ( $_POST[ $this->g_html_name ] );
                    if ( $field_value == '' || $field_value == '0'
                        || ! Common::validateFieldType ( $this->g_target_data_type, $field_value, $this->g_compulsory, $this->g_min_length ) ) {
                        if ( $return ) {
                            $return = false;
                        }
                        $this->valid = false;
                    }
                }
            }
            return $return;
        }
        if ( isset ( $_GET[ $this->g_html_name ] ) ) {
            if ( $this->g_compulsory ) {
                $field_value = trim ( $_GET[ $this->g_html_name ] );
                if ( $field_value == '' || $field_value == '0' || ! Common::validateFieldType ( $this->g_target_data_type, $field_value, $this->g_compulsory, $this->g_min_length ) ) {
                    if ( $return ) {
                        $return = false;
                    }
                    $this->valid = false;
                }
            }
        }
        return $return;
    }
}
