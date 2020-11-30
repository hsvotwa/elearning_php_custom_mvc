<?php
    class BaseMdl {
        public  $g_row = null,
                $g_fields = null,
                $g_gen = null,
                $g_invalid_fields = null,
                $g_errors = null,
                $g_mysql = null,
                $g_entity_name = "",
                $g_retrieve_query = "",
                $g_sql_table = "",
                $g_additional_sql = "",
                $g_uuid = "";

        public function getMySql() {
            if ( is_null ( $this->g_mysql ) ) {
                $this->g_mysql = new MySql();
            }
            return $this->g_mysql;
        }
    
        public function getGen() {
            if ( is_null ( $this->g_gen ) ) {
                $this->g_gen = new GeneralDisplay ( null, null );
            }
            return $this->g_gen;
        }

        public function retrieve () {
            $result = $this->getMySql()->getQryRlt (  $this->g_retrieve_query );
            if ( ! $result || ! mysqli_num_rows ( $result ) ) {
                return null;
            }
            $row = mysqli_fetch_assoc ( $result );
            if ( ! $row ) {
                return false;
            }
            $this->g_row = $row;
            return ! is_null ( $this->g_row );
        }

        public function set () {
            if ( ! $_POST ) {
                return false;
            }
            if ( ! $this->g_fields ) {
                return false;
            }
            $fields_valid = $this->validField();
            if ( ! $fields_valid || ! empty( $this->g_invalid_fields ) ) {
                $error_mess = empty( $this->g_invalid_fields ) ? "Some fields are invalid." : "Invalid field(s): ";
                if ( ! empty( $this->g_invalid_fields ) ) {
                    $err_field_descr = array();
                    foreach( $this->g_invalid_fields as $field ) {
                        $err_field_descr[] = $field->g_description;
                    }
                    $error_mess .= implode( ", ", $err_field_descr );
                }
                $this->g_errors[] = $error_mess;
                return false;
            }
            $uuid = (
                isset( $_POST['uuid'] ) && !empty( $_POST['uuid'] )
                ? $_POST['uuid']
                : null
            );
            $this->g_id = $uuid;
            if ( ! $this->upd( $uuid ) ) {
                $this->g_errors[] = EXCEPTION_MESSAGE;
                return false;
            }
            return $this->retrieve();
        }
    
        public function upd( &$uuid ) {
            ! $uuid && $uuid = $this->getMySql()->getUuid();
            if ( ! $uuid || ! $this->g_fields ) {
                return false;
            }
            // echo  $this->getMySql()->getUpdateQuery( 
            //     $this->g_sql_table, 
            //     $uuid, 
            //     $this->g_fields, 
            //     EnumSqlQryType::none, 
            //     true,
            //     $this->g_additional_sql
            // );
            return $this->getMySql()->getQryRlt ( 
                $this->getMySql()->getUpdateQuery( 
                    $this->g_sql_table, 
                    $uuid, 
                    $this->g_fields, 
                    EnumSqlQryType::none, 
                    true,
                    $this->g_additional_sql )
                );
        }
    
        public function trackChange ( $uuid ) {
            $audit_trail = new AuditTrail(
                                            UserSessionMdl::getUuid(),
                                            $uuid,
                                            $this->g_fields
                                        );
            $audit_trail->setMySQL ( $this->getMySql() );
            if ( $this->g_row ) {
                $audit_trail->setCompResult ( $this->g_row );
            } else {
                $audit_trail->setNew( true );
            }
            $audit_trail->setAuditUuid ( $uuid );
            return $audit_trail->trackChanges();
        }

        public function validField() {
            if ( ! is_array ( $this->g_fields ) ) {
                return false;
            }
            $return = true;
            foreach ( $this->g_fields as $field ) {
                $valid = $field->validate();
                if( ! $valid ) {
                    $this->g_invalid_fields[] = $field;
                    $return && $return = false;
                }
            }
            return $return;
        }
    }