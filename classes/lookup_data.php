<?php
class LookupData {
    public static function getAllstudentList() {
        $query = "select 
                    uuid as value, 
                    name 
                from tbl_student 
                where profile_uuid = '" . UserSessionMdl::getProfileId() . "'
                order by name";
        return ( new MySql() )->getQryRlt( $query );
    }

    public static function getUnlinkedstudentList( $search_text ) {
        $query = "select 
                    o.uuid as value, 
                    concat(surname, ' ', name, ' (', id_no, ')') as name 
                from tbl_student o
                where uuid not in (
                        select student_uuid 
                            from tbl_unit_student 
                            where soft_del = " . EnumYesNo::no . "
                    )
                and profile_uuid = '" . UserSessionMdl::getProfileId() . "'
                and ( name like '%$search_text%' or surname like '%$search_text%' )
                group by o.uuid, concat(surname, ' ', name)
                order by o.surname, o.name";
        return ( new MySql() )->getQryRlt( $query );
    }
}