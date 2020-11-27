<?php
class studentMgr extends BaseMgr {
    public function __construct ( $unit_uuid = "",  $search_text = "" ) {
        $this->g_entity_name = "student(s)";
        if ( $unit_uuid ) {
            $this->g_retrieve_query = $this->getRetrieveQueryPerUnit ( $unit_uuid );
        } else {
            $this->g_retrieve_query = $this->getRetrieveQuery( $search_text );
        }
    }

    protected function getRetrieveQueryPerUnit ( $unit_uuid ) {
         return "select 
                    o.*, 
                    uo.uuid as unit_student_uuid,
                    uo.is_tenant,
                    yn.name as is_tenant_desc
                from tbl_student o
                inner join tbl_unit_student uo on uo.student_uuid = o.uuid
                inner join tbl_lu_yes_no yn on yn.enum_id = uo.is_tenant
                where uo.unit_uuid = '$unit_uuid'
                and uo.soft_del = " . EnumYesNo::no . "
                order by o.surname, o.name";
    }

    function validIdNum( $id_no, $uuid ) {
        $query = "select * from tbl_student where id_no = '$id_no' and uuid != '$uuid';";
        $data = $this->getMySql()->getQueryResult( $query );
       return ! $data || ! $data->num_rows;
    }


    protected function getRetrieveQuery ( $search_text ) {
        return "select 
                    o.*
               from tbl_student o
               where ( o.name like '%$search_text%' or surname like '%$search_text%' or id_no like '%$search_text%' )
               and o.profile_uuid = '" . UserSessionMdl::getProfileId() . "'
               order by o.surname, o.name";
   }
}