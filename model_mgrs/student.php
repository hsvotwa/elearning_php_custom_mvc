<?php
class StudentMgr extends BaseMgr {
    public function __construct( $search_text = "") {
        $this->g_entity_name = "student(s)";
        $this->g_retrieve_query = $this->getRetrieveQuery( $search_text );
    }

    protected function getRetrieveQuery( $search_text ) {
        return "select s.*, ls.name as status 
                    from tbl_student s
                    inner join tbl_lu_student_status ls on ls.enum_id = s.status_id
                where ( s.name like '%$search_text%' or s.tel_no like '%$search_text%' )
                and status_id != " . EnumStudentStatus::draft. "
                order by s.name";
    }

    public function getNextStudentNumber() {
        $query = "select ifnull(count(uuid), 0) + 1 as student_count
                    from tbl_student
                    order by name";
        $data = $this->getMySql()->getQueryResult( $query );
       if( ! $data || ! $data->num_rows ) {
           return "S0001";
       }
       $data = mysqli_fetch_array( $data );
       return str_pad( $data["student_count"], 4, '0', STR_PAD_RIGHT );
    }

    function validEmail( $email, $uuid ) {
        $query = "select * from tbl_student where email = '$email' and uuid != '$uuid' and status_id != " . EnumStudentStatus::draft;
        $data = $this->getMySql()->getQueryResult( $query );
       return ! $data || ! $data->num_rows;
    }
}