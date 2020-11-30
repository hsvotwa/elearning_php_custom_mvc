<?php
class StudentMdl extends BaseMdl {
    public function __construct ( $id = null, $check_profile = true ) {
        $this->g_id = $id;
        $this->g_entity_name = "Student";
        $this->g_sql_table = EnumSqlTbl::tbl_student;
        $this->g_retrieve_query = $this->getRetrieveQuery( $check_profile );
        $this->g_fields = $this->g_invalid_fields = $this->g_errors = array ();
        if ( $id ) {
            $this->retrieve ();
        } else {
            $this->g_id = $this->getMySql()->getUuid();
        }
        $this->getFields();
    }

    protected function getRetrieveQuery( $check_profile = true ) {
        return "select *
                    from $this->g_sql_table u
                where uuid = '$this->g_id'";
    }

    function getSubjects() {
        $query = "select * 
                    from tbl_subject where id in (
                        select subject_id from tbl_student_subject 
                        where student_uuid = '$this->g_id' 
                        and soft_deleted != " . EnumYesNo::yes . "
                    );";
                    echo  $query;
        return $this->getMySql()->getQueryResult( $query );
    }

    function validQuotation(&$error_message) {
        $return = array();
        //Subject cost
        $query = "select *
                    from tbl_student_subject 
                    where student_uuid = '$this->g_id' 
                    and soft_deleted != " . EnumYesNo::yes;
        $res = $this->getMySql()->getQueryResult( $query );
        $total_cost = 0;
        if( ! $res || ! $res->num_rows ) {
            $error_message = "You are required to select at least one subject.";
            return false;
        }
        //Subject cost
        $query = "select *
                    from tbl_student_aid 
                    where student_uuid = '$this->g_id' 
                    and soft_deleted != " . EnumYesNo::yes;
        $res = $this->getMySql()->getQueryResult( $query );
        if( ! $res || ! $res->num_rows ) {
            $error_message = "You are required to select at least one study aid.";
            return false;
        }
        return true;
    }

    function getQuotation() {
        $return = array();
        //Subject cost
        $query = "select ifnull(sum(cost), 0) cost
                    from tbl_student_subject 
                    where student_uuid = '$this->g_id' 
                    and soft_deleted != " . EnumYesNo::yes;
        $res = $this->getMySql()->getQueryResult( $query );
        $total_cost = 0;
        if( $res && $res->num_rows ) {
            $return["subject_cost"] = mysqli_fetch_array( $res )["cost"];
            $total_cost += $return["subject_cost"];
        }
        //Subject cost
        $query = "select ifnull(sum(cost), 0) cost 
                    from tbl_student_aid 
                    where student_uuid = '$this->g_id' 
                    and soft_deleted != " . EnumYesNo::yes;
        $res = $this->getMySql()->getQueryResult( $query );
        if( $res && $res->num_rows ) {
            $return["aid_cost"] = mysqli_fetch_array( $res )["cost"];
            $total_cost += $return["aid_cost"];
        }
        $return["total_cost"] = $total_cost;
        $return["interest_percent"] = $this->getInterestRate(  $this->g_row["payment_term_id"] );
        $return["interest_amount"] = $total_cost * ( $return["interest_percent"] / 100 );
        $return["total_due"] = $total_cost + $return["interest_amount"];
        $return["period"] = $this->g_row["payment_term_id"] . ( 
            $this->g_row["payment_term_id"] > 1 ? " years" : " year"   
        );
        $return["monthly_payment"] =  ! $this->g_row["payment_term_id"] ? 0 : $return["total_due"] / ( $this->g_row["payment_term_id"] * 12 ); //Avpid divide by zero exception
        return $return;
    }

    private function getInterestRate( $payment_term_id ) {
        switch ( $payment_term_id ) {
            case EnumPaymentTerm::one_year:
                return 15;
            case EnumPaymentTerm::two_years:
                return 16;
            case EnumPaymentTerm::three_years:
                return 17;
        }
        return 0;
    }

    function getAids() {
        $query = "select * 
                    from tbl_study_aid where id in (
                        select aid_id from tbl_student_aid 
                        where student_uuid = '$this->g_id' 
                        and soft_deleted != " . EnumYesNo::yes . "
                    );";
        return $this->getMySql()->getQueryResult( $query );
    }

    public function getRecordPageTitle() {
        return ( ! is_null ( $this->g_row ) ? $this->g_entity_name . ': ' . $this->g_row['name']  : 'Register as a ' . $this->g_entity_name );
    }

    public function getFields() {
        if ( $this->g_fields != null ) {
            return $this->g_fields;
        }
        $return = array ();
        $return["name"] = new FieldMdl( 
            "name", "name", "Name", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["tel_no"] = new FieldMdl( 
            "tel_no", "tel_no", "Telephone", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["email"] = new FieldMdl( 
            "email", "email", "Email", true, EnumFieldDataType::_string, EnumFieldType::_string, $this->g_sql_table, true, "text", $this->g_row
        );
        $return["payment_term_id"] = new FieldMdl( 
            "payment_term_id", "payment_term_id", "Payment option", true, EnumFieldDataType::_string, EnumFieldType::_select, $this->g_sql_table, true, "text", $this->g_row, 2, null, LookupData::getPaymentTermList(), "-- select payment option --"
        );
        $return["status_id"] = new FieldMdl( 
            "status_id", "status_id", "Status", true, EnumFieldDataType::_string, EnumFieldType::_select, $this->g_sql_table, true, "text", $this->g_row, 2, null, LookupData::getPaymentTermList(), "-- select payment option --"
        );
        $this->g_fields = $return;
        return $this->g_fields;
    }
    
    //Subject(s)
    public function studentHasSubject( $subject_id, $student_uuid ) {
        $query = "select * 
                        from tbl_student_subject
                    where subject_id = '$subject_id'
                    and student_uuid = '$student_uuid'
                    and soft_deleted = " . EnumYesNo::no . ";";
        $existing = $this->getMySql()->getQueryResult( $query );
        return $existing && $existing->num_rows;
    }

    public function linkSubject() {
        if( $this->studentHasSubject( $_POST["subject_id"], $_POST["student_uuid"]) ) {
            return true;
        }
        $query = "insert into tbl_student_subject
                    set uuid = uuid(),
                    subject_id = '" . $_POST["subject_id"] . "',
                    student_uuid = '" . $_POST["student_uuid"] . "',
                    cost = (select ifnull(cost, 0) from tbl_subject where id = '" . $_POST["subject_id"] . "'),
                    soft_deleted = " . EnumYesNo::no . ",
                    created = now(),
                    last_modified = now();";
        return  $this->getMySql()->getQueryResult( $query );
    }

    public function removeSubject( &$error_message ) {
        if( ! $_POST ) {
            return false;
        }
        $query = "update tbl_student_subject
                    set soft_deleted = " . EnumYesNo::yes . ",
                    last_modified = now()
                where uuid = '" . $_POST["student_subject_uuid"] . "';";
       return $this->getMySql()->getQueryResult( $query );
    }

    //Study aid(s)
    public function studentHasAid( $aid_id, $student_uuid ) {
        $query = "select * 
                        from tbl_student_aid
                    where aid_id = '$aid_id'
                    and student_uuid = '$student_uuid'
                    and soft_deleted = " . EnumYesNo::no . ";";
        $existing = $this->getMySql()->getQueryResult( $query );
        return $existing && $existing->num_rows;
    }

    public function linkAid() {
        if( $this->studentHasAid( $_POST["aid_id"], $_POST["student_uuid"] ) ) {
            return true;
        }
        $query = "insert into tbl_student_aid
                    set uuid = uuid(),
                    aid_id = '" . $_POST["aid_id"] . "',
                    student_uuid = '" . $_POST["student_uuid"] . "',
                    cost = (select ifnull(cost, 0) from tbl_study_aid where id = '" . $_POST["aid_id"] . "'),
                    soft_deleted = " . EnumYesNo::no . ",
                    created = now(),
                    last_modified = now();";
        return  $this->getMySql()->getQueryResult( $query );
    }

    public function removeAid( &$error_message ) {
        if( ! $_POST ) {
            return false;
        }
        $query = "update tbl_student_aid
                    set soft_deleted = " . EnumYesNo::yes . ",
                    last_modified = now()
                where uuid = '" . $_POST["student_aid_uuid"] . "';";
       return $this->getMySql()->getQueryResult( $query );
    }

    public function approveApplication() {
        if( ! $_POST["student_uuid"] ) {
            return false;
        }
        $queries = array();
        $password = UserMgr::createPassword ( 10 );
        $queries[] = "insert into tbl_user
                        set uuid = uuid(),
                        student_uuid = '" . $_POST["student_uuid"] . "',
                        email = '" . $this->g_row["email"] . "',
                        user_type_id = '" . EnumUserRoleType::student . "',
                        status_id = '" . EnumStatus::active . "',
                        password = '" . md5( $password ) . "',
                        created = now(),
                        last_modified = now();";
        $queries[] = "update tbl_student 
                        set status_id = " . EnumStudentStatus::active . ", 
                        last_modified = now() 
                        where uuid = '" . $_POST["student_uuid"] . "' ";
        return  $this->getMySql()->getQueryResult( $queries ) && $this->notifyStudent( true, $password );
    }

    public function declineApplication() {
        if( ! $_POST["student_uuid"] ) {
            return false;
        }
        $query = "update tbl_student 
                        set status_id = " . EnumStudentStatus::declined . ", 
                        last_modified = now() 
                        where uuid = '" . $_POST["student_uuid"] . "' ";
        return  $this->getMySql()->getQueryResult( $query ) && $this->notifyStudent( false, null );
    }

    private function notifyStudent( $success, $password ) {
        $subject = APP_NAME .' - ' . ( $success ? "Congratulation on your enrolment :-)" : "Enrolment declined :-(" );
        $message = 'Hi ' . $this->g_row["name"] . ',<br/><br/>';
        if(  $success ) {
            $message .= "Following the above, we are excited to notify you that your student account has also been created. Herewith credentials for the new " . APP_NAME . " account:";
            $message .= '<br/>Username: ' . $this->g_row["email"];
            $message .= '<br/>Password: ' . $password;
        } else {
            $message .= "We inform you of the above with regret. Please try again next year.";
        }
        $message .= '<br/><br/> Regards,';
        $message .= '<br/>' . APP_SHORT_NAME . '.';
        require_once( ROOT . 'mailer/php_mailer.php' );
        $mailer = new PHPMailer ( true );
        $mailer->IsSMTP ();
        $mailer->SMTPDebug = SMTP_DEBUG;
        $mailer->SMTPAuth = SMTP_AUTH;
        $mailer->SMTPSecure = SMTP_SECURE;
        $mailer->Host = SMTP_HOST;
        $mailer->Port = SMTP_PORT;
        $mailer->IsHTML ( true );
        $mailer->Username = SMTP_USERNAME;
        $mailer->Password = SMTP_PASSWORD;
        $mailer->SetFrom ( SMTP_FROM_ADDRESS );
        $mailer->FromName = COMPANY_NAME;
        $mailer->CharSet = "UTF-8";
        $mailer->ClearAllRecipients ();
        $mailer->AddAddress ( $this->g_row["email"] );
        $mailer->Subject = $subject;
        $body =  $message;
        $mailer->MsgHTML ( $body );
        return $mailer->Send();
    }

}