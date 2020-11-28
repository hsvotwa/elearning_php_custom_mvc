<?php
class LookupData {
    public static function getAllStudentList() {
        $query = "select 
                    uuid as value, 
                    name 
                from tbl_student
                order by name";
        return ( new MySql() )->getQryRlt( $query );
    }

    public static function getRoleTypeList() {
        $query = "select 
                    uuid as value, 
                    name 
                from tbl_lu_role_type
                order by name";
        return ( new MySql() )->getQryRlt( $query );
    }
}