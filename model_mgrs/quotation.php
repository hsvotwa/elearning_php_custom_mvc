<?php
class ProfileMgr extends BaseMgr {
    public function __construct ( $search_text = "", $invite_list = false ) {
        $this->g_entity_name = ! $invite_list ? "profile(s)" : " profile invite(s)" ;
        $this->g_retrieve_query = $this->getRetrieveQuery( $search_text,  $invite_list );
    }

    protected function getRetrieveQuery ( $search_text, $invite_list ) {
        $query = "select p.*, upa.confirmation_code
                from tbl_profile p
                inner join tbl_user_profile_access upa on upa.profile_uuid = p.uuid
                where ( p.name like '%$search_text%' or p.tel_no like '%$search_text%' )
                and upa.soft_del = " . EnumYesNo::no . "
                and upa.user_uuid = '" . UserSessionMdl::getUuid() . "' ";
        if( $invite_list ) {
            $query .= " and ifnull( upa.confirmation_code, '') != '' ";
        } else {
            $query .= " and ifnull( upa.confirmation_code, '') = '' ";
        }
        $query .= " order by p.name";
        return $query;
    }
}