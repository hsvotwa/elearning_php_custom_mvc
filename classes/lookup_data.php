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

    public static function getPaymentTermList() {
        $query = "select 
                    enum_id as value, 
                    name 
                from tbl_lu_payment_term
                order by name";
        return ( new MySql() )->getQryRlt( $query );
    }
    
    public static function getUnlinkedSubjectList( $search_text, $student_uuid ) {
        $query = "select 
                    id as value, 
                    name 
                from tbl_subject
                where id not in (select subject_id from tbl_student_subject where student_uuid = '" . $student_uuid . "')
                and (name like '%" . $search_text . "%' or code like '%" . $search_text . "%')
                order by code";
        return ( new MySql() )->getQryRlt( $query );
    }

    public static function getUnlinkedStudyAidList( $search_text, $student_uuid ) {
        $query = "select 
                    id as value, 
                    name 
                from tbl_study_aid
                where id not in (select aid_id from tbl_student_aid where student_uuid = '" . $student_uuid . "')
                and (name like '%" . $search_text . "%' or code like '%" . $search_text . "%')
                order by code";
        return ( new MySql() )->getQryRlt( $query );
    }
}