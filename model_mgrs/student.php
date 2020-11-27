<?php
class studentMgr extends BaseMgr {
    public function __construct( $search_text = "") {
        $this->g_entity_name = "student(es)";
        $this->g_retrieve_query = $this->getRetrieveQuery( $search_text );
    }

    protected function getRetrieveQuery( $search_text ) {
        return "select * 
                from tbl_student
                where ( name like '%$search_text%' or tel_no like '%$search_text%' )
                and profile_uuid = '" . UserSessionMdl::getProfileId() . "'
                order by name";
    }
}