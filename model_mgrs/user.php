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
                inner join tbl_user_profile_access upa on upa.user_uuid = u.user_uuid
                where ( name like '%$search_text%' or surname like '%$search_text%' )
                and upa.soft_del = " . EnumYesNo::no . "
                order by surname";
    }

    public static function createPassword ( $password_length = 10 ) {
        $return = '';
        $chars = '234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ( $char_count = 1; $char_count <= $password_length; $char_count++ ) {
            $return .= $chars { mt_rand( 0, strlen ( $chars ) ) };
        }
        return $return;
    }
}
?>