<?php
class MySql {
    private     $g_dbhost = MYSQL_HOST,
                $g_dbuser = MYSQL_USR,
                $g_pwd = MYSQL_PWD,
                $g_dbname = MYSQL_DB;
    private static $g_conn = null;

    public function getQueryResult ( $queries, &$insert_id = '' ) {
        $this->openConnection();
        if ( ! is_array ( $queries ) ) {
            $return = mysqli_query( self::$g_conn, $queries );
            $insert_id = mysqli_insert_id( self::$g_conn );
            return $return;
        }
        foreach ( $queries as $query ) {
            $return = mysqli_query( self::$g_conn, $query );
        }
        return $return;
    }

    public function getQryRlt ( $queries, &$insert_id = '' ) {
        return $this->getQueryResult ( $queries, $insert_id );
    }

    public function openConnection() {
        if ( ! self::$g_conn ) {
            self::$g_conn = @mysqli_connect ( $this->g_dbhost, $this->g_dbuser, $this->g_pwd );
            if ( self::$g_conn ) {
                mysqli_select_db( self::$g_conn, $this->g_dbname );
                mysqli_query( self::$g_conn, "set names 'UTF8'" );
            }
        }
        return self::$g_conn;
    }

    public function conn() {
        return $this->openConnection();
    }

    private function closeConnection() {
        if ( self::$g_conn ) {
            @mysqli_close( self::$g_conn );
            self::$g_conn = null;
        }
    }

    public function getUpdateQuery ( $table, $uuid, $fields, $statement_type = EnumSqlQryType::none, $is_post = true, $add_col_upd = '', $set_null_if_empty = false ) {
        $return = '';
        if ( ! is_array ( $fields ) ) {
        	return $return;
        }
        $count = ( count ( $fields ) - 1 );
        foreach ( $fields as $field ) {
            if ( strtolower ( $field->g_mysql_tbl ) == strtolower ( $table ) ) {
                if ( $is_post ) {
                    if ( isset ( $_POST[ $field->g_html_name ] ) || $field->g_target_field_type == EnumFieldType::_checkbox ) {
                        $return .= $field->g_mysql_name . '=' ;
                        $raw_value = isset ( $_POST[ $field->g_html_name ] ) ? $_POST[ $field->g_html_name ] : null;
                        if ( $field->g_target_field_type == EnumFieldType::_checkbox ) {
                            $raw_value = $raw_value && $raw_value == "on" ? EnumYesNo::yes : EnumYesNo::no;
                        }
                        $value = $this->getColumnUpdateValue(
                                                                $raw_value,
                                                                (int) $field->g_target_data_type
                                                           );
                        if ( $set_null_if_empty && empty ( $value ) ) {
                            $value = 'null';
                        }
                        $return .= $value;
                        $return .= ',';
                    }
                } else if ( isset ( $_GET[ $field->g_html_name ] ) ) {
                    $return .= $field->g_mysql_name . '=';
                    $value = $this->getColumnUpdateValue(
                                                            $_GET[ $field->g_html_name ],
                                                            (int) $field->g_target_data_type
                                                        );
                    if ( $set_null_if_empty && empty ( $value ) ) {
                        $value = 'null';
                    }
                    $return .= $value;
                    $return .= ',';
                }
            }
        }
        if ( $add_col_upd == ''
                && substr ( $return, strlen ( $return ) - 1, 1) == ',' ) {
            $return = substr ( $return, 0, strlen ( $return ) - 1 );
        } elseif ( $add_col_upd != '' ) {
            if ( substr ( $return, strlen ( $return ) - 1, 1 ) == ',' ) {
                $return .= $add_col_upd;
            } else {
                $return .= ',' . $add_col_upd;
            }
        }
        switch ( $statement_type ) {
            case EnumSqlQryType::none: 
                $query = "select *
                                from $table
                            where (uuid='$uuid' );";
                $result = $this->getQueryResult ( $query );
                if ( mysqli_num_rows ( $result ) > 0 ) {
                    $return = "update $table
                                set last_modified=now(),
                                $return
                                where (uuid='$uuid' )";
                } else {
                    $return = "insert into $table
                                set uuid='$uuid',
                                created=now(),
                                last_modified=now(),
                                $return";
                }
                break;
            case EnumSqlQryType::update:  //Create update.
                $return = "update $table
                            set last_modified=now(),
                            $return
                            where (uuid='$uuid' )";
                break;
            case EnumSqlQryType::insert:
                $return = "insert into $table
                            set uuid='$uuid',
                            created=now(),
                            last_modified=now(),
                            $return";
                break;
        }
        $return .= ';';
        return $return;
    }

    private function getColumnUpdateValue ( $value, $convert_to ) {
        $value = trim ( $value );
        if ( strtolower ( $value ) == 'null' ) {
        	return 'null';
    	}
        switch ( $convert_to ) {
            case EnumFieldType::_string:
                return "'" . Convert::toString ( $value, true ) . "'";
            case EnumFieldType::_integer:
            case EnumFieldType::_double:
            case EnumFieldType::_float:
                return Convert::toNum ( $value, true );
            case EnumFieldType::_datetime:
                return "'" . Convert::toDate ( $value, true ) . "'";
            case EnumFieldType::_date:
                return "'" . Convert::toDate ( $value, true, true ) . "'";
        }
        return '';
    }

    public function getColumnValue ( $value, $convert_to, $default_value, $num_decimal = 2 ) {
        $return = null;
        if ( ! is_null ( $value ) ) {
            switch ( $convert_to ) {
                case EnumFieldType::_string:
                    return Convert::toString ( $value );
                case EnumFieldType::_integer:
                   	return Convert::toNum ( $value, false, 0 );
                case EnumFieldType::_double:
                case EnumFieldType::_float:
                    $return = (double) Convert::toNum ( $value, false, $num_decimal );
                    if ( $return < 0 ) {
                        $return = '(' . str_replace( '-', '', $return ) . ' )';
                    }
                    return $return;
                case EnumFieldType::_datetime:
                    return Convert::toDate ( $value );
                case EnumFieldType::_date:
                    return Convert::toDate ( $value, false, true );
            }
             return $return;
        }
        return $default_value;
    }

    public function colExists( $tbl, $col, $db = null ) {
        if ( ! isset ( $db) ) {
            $db = MYSQL_DB;
        }
        $qry = "select
                    count(*) _count
                from information_schema.columns
                where (table_schema = '{$db}' )
                and (table_name = '{$tbl}' )
                and (column_name = '{$col}' );";
        $result = $this->getQueryResult ( $qry );
        if ( $row = $result->fetch_assoc() ) {
            $result->free_result();
            return (1 == $row['_count'] );
        }
        return false;
    }

    public function tblExists( $tbl, $tbl_type = 'BASE TABLE', $db = null ) {
        if ( ! isset ( $db ) ) {
            $db = MYSQL_DB;
        }
        $qry = "select
                    count(*) _count
                from information_schema.tables
                where (table_schema = '{$db}' )
                and (table_type = '{$tbl_type}' )
                and (table_name = '{$tbl}' );";
        $result = $this->getQueryResult ( $qry );
        if ( $row = $result->fetch_assoc() ) {
            $result->free_result();
            return ( 1 == $row['_count'] );
        }
        return false;
    }

    public function indexExists ( $tbl, $index ) {
        $qry = "show index from {$tbl} where KEY_NAME = '" . Convert::toString ( $index, true ) . "';";
        $result = $this->getQueryResult ( $qry );
        if ( $result && $row = $result->fetch_assoc() ) {
            $result->free_result();
            return true;
        }
        return false;
    }

    public function tblRename ( $old, $new ) {
        if ( $this->tblExists ( $old ) && !$this->tblExists ( $new ) ) {
            $qry = "rename table {$old} to {$new};";
            return $this->getQueryResult ( $qry );
        }
        return false;
    }

    public function tblColRename ( $table, $old, $new, $info ) {
        if ( $this->tblExists ( $table ) ) {
            if ( ! $this->colExists ( $table, $old ) ) {
                return true;
            }
            $qry = "alter table {$table} change `{$old}` `{$new}` {$info};";
            return $this->getQueryResult ( $qry );
        }
        return false;
    }

    public function tblColDelete ( $table, $name) {
        if ( $this->tblExists ( $table ) ) {
            if ( ! $this->colExists ( $table, $name ) ) {
                return true;
            }
            $qry = "alter table {$table} drop `{$name}`;";
            return $this->getQueryResult ( $qry );
        }
        return false;
    }

    public function tblDelete ( $table ) {
        if ( $this->tblExists ( $table ) ) {
            $qry = "drop table `{$table}`;";
            return $this->getQueryResult ( $qry );
        }
        return false;
    }

    public function getUuid() {
        $this->openConnection();
        $row = mysqli_fetch_assoc ( mysqli_query( self::$g_conn, 'select uuid() uuid;' ) );
        return $row ? (string) $row['uuid'] : '';
    }

    public function getIdUuid ( $key, $table_name, $enum = EnumIdUuid::uuid ) {
       if ( empty ( $key ) || empty ( $enum ) || empty ( $table_name ) ) {
           return '';
        }
        $query = "select
                        " . ( $enum == EnumIdUuid::id ? 'id' : 'uuid' ) . " value
                    from $table_name
                    where " . ( $enum == EnumIdUuid::id ? "uuid = '$key' " : "id = $key " ) . " ";
        $row = mysqli_fetch_assoc ( $this->getQryRlt ( $query ) );
        return $row ? (string) $row['value'] : '';
    }

    public function __destruct() {
        $this->closeConnection();
    }
}