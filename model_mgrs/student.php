<?php
class StudentMgr extends BaseMgr {
    public function __construct( $search_text = "") {
        $this->g_entity_name = "student(es)";
        $this->g_retrieve_query = $this->getRetrieveQuery( $search_text );
    }

    protected function getRetrieveQuery( $search_text ) {
        return "select * 
                from tbl_student
                where ( name like '%$search_text%' or tel_no like '%$search_text%' )
                order by name";
    }

    function validEmail( $email, $uuid ) {
        $query = "select * from tbl_student where email = '$email' and uuid != '$uuid';";
        $data = $this->getMySql()->getQueryResult( $query );
       return ! $data || ! $data->num_rows;
    }
}