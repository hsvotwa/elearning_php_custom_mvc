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
    
    public static function getUnlinkedSubjectList( $search_text, $student_uuid ) {
        $query = "select 
                    uuid as value, 
                    name 
                from tbl_subject
                where uuid not in (select uuid from tbl_student_subject where student_uuid = '" . $student_uuid . "')
                and name like '%" . $student_uuid . "%'
                order by code";
        return ( new MySql() )->getQryRlt( $query );
    }

    public static function getUnlinkedStudyAidList( $search_text, $student_uuid ) {
        $query = "select 
                    uuid as value, 
                    name 
                from tbl_study_aid
                where uuid not in (select uuid from tbl_student_aid where student_uuid = '" . $student_uuid . "')
                and name like '%" . $student_uuid . "%'
                order by code";
        return ( new MySql() )->getQryRlt( $query );
    }
}