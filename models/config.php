<?php
class ConfigMdl extends BaseMdl {
    public $g_config_values = null;

    public function __construct ( $enum_ids ) {
        $this->g_sql_table = EnumSqlTbl::tbl_config;
        $this->g_retrieve_query = $this->getRetrieveQuery( $enum_ids );
        if ( ! is_array( $enum_ids ) ) {
            $enum_ids = array( $enum_ids );
            $this->retrieve();
        } else {
            $this->g_id = implode( ",", $enum_ids );
            $this->getAll();
        }
    }

    protected function getAll() {
        $this->g_config_values = $this->getMySql()->getQueryResult( "select * from $this->g_sql_table where enum_id in( $this->g_id );" );
    }

    protected function getRetrieveQuery( $enum_id ) {
       return "select * from $this->g_sql_table where enum_id = $enum_id;" ;
    }
}