<?php
class UserMgr extends BaseMgr {
    public function __construct ( $search_text = "") {
        $this->g_entity_name = "user(s)";
        $this->g_retrieve_query = $this->getRetrieveQuery( $search_text );
    }

    protected function getRetrieveQuery ( $search_text ) {
        return "select u.*, 
                    upa.confirmation_code 
                from tbl_user u
                inner join tbl_user_profile_access upa on upa.user_uid = u.user_uuid
                where ( name like '%$search_text%' or surname like '%$search_text%' )
                and upa.soft_del = " . EnumYesNo::no . "
                order by surname";
    }
}
?>