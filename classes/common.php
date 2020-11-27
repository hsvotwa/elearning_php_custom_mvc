<?php
class Common {
    public static function isLiveServer() {
        $dev_domains = array (
            'localhost', '127.0.0.1', 'hsvotwa.local'
       );
       $http_host = strtolower ( $_SERVER['HTTP_HOST'] );
       foreach ( $dev_domains as $dev_domain ) {
           if ( stristr ( $http_host, $dev_domain ) ) {
               return false;
           }
       }
       return true;
    }

    public static function redirect ( $page ) {
        echo "<script> window.location = '$page';</script>";
        exit();
    }

    public static function getRedirect ( $page ) {
        echo "window.location = '$page';";
        exit();
    }

    public static function endsWith( $string, $end_string ) { 
        $len = strlen( $end_string ); 
        if ( $len == 0 ) { 
            return true; 
        } 
        return ( substr( $string, -$len ) === $end_string ); 
    } 

    public static function validateFieldType ( $target_type, $field_value, $compulsory, $min_length ) {
        switch ( $target_type ) {
            case EnumFieldType::_date:
            case EnumFieldType::_datetime:
            case EnumFieldType::_string:
                return is_string ( $field_value ) && ( $field_value == "" || $min_length == 0 || strlen( $field_value ) >= $min_length );
            case EnumFieldType::_integer:
            case EnumFieldType::_double:
            case EnumFieldType::_float:
                return is_numeric( str_replace( ',', '', $field_value ) ) && 
                        ( ! $compulsory || str_replace( ',', '', $field_value ) > 0 ) && 
                        ( $field_value == "" || $min_length == 0 || strlen( $field_value ) >= $min_length );
            case  EnumFieldType::_email:
                return Validate::isEmail ( $field_value, /*verify_domain=*/ false );
            default:
                return false;
        }
    }

    public static function hasFieldBeenUpdated( MySql $mysql, $fields, $uuid, $table, $is_post = true ) {
        $query = "select *
                        from $table
                        where (uuid='$uuid' );";
        $row = mysqli_fetch_assoc ( $mysql->getQryRlt ( $query ) );
        if ( ! is_array ( $fields ) ) {
            return false;
        }
        if ( ! $row ) {
            return true;
        }
        $count = count ( $fields );
        for ( $i = 0; $i < $count; $i++ ) {
            $field = $fields[ $i ];
            if ( strtolower ( $field->g_mysql_tbl ) == strtolower ( $table ) ) {
                if ( $is_post ) {
                    if ( isset ( $_POST[ $field->g_html_name ] ) ) {
                        $value = (string) (
                                        $field->g_target_data_type != EnumFieldType::_double
                                            && $field->g_target_data_type != EnumFieldType::_double
                                        ? $_POST[ $field->g_html_name ]
                                        : str_replace( ',', '', $_POST[ $field->g_html_name ] )
                                    );
                        if ( (string) $row[ $field->g_sql_name] != $value ) {
                            return true;
                        }
                    }
                } else {
                    if ( isset ( $_GET[ $field->g_html_name ] ) ) {
                        $value = (string) (
                            $field->g_target_data_type != EnumFieldType::_double
                                        && $field->g_target_data_type != EnumFieldType::_float
                                    ? $_GET[ $field->g_html_name ]
                                    : str_replace( ',', '', $_GET[ $field->g_html_name ] )
                                );
                        if ( (string) $row[ $field->g_sql_name ] != $value ) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public static function isFieldValid ( $fields, $html_field ) {
        if ( is_array ( $fields ) ) {
            foreach ( $fields as $field ) {
                if ( strtolower ( $html_field ) == strtolower ( $field->g_html_name ) ) {
                    return (bool) $field->g_valid;
                }
            }
        }
        return true;
    }

    public static function isFieldInvalid ( $invalid_fields, $html_field ) {
        if ( is_array ( $invalid_fields ) ) {
            foreach ( $invalid_fields as $field ) {
                if ( strtolower ( $html_field ) == strtolower ( $field->g_html_name ) ) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getFieldFromArray ( $fields, $html_field, &$out_field ) {
      if ( is_array ( $fields ) ) {
        $count = count ( $fields );
           for ( $i = 0; $i < $count; $i++ ) {
                $field = $fields[$i];
                if ( strtolower ( $html_field ) == strtolower ( $field->g_html_name ) ) {
                    $out_field = $field;
                    return true;
                }
            }
        }
        return false;
    }

    public static function isPostFieldEmpty ( $fields, $valid_empty = true ) {
        ! is_array ( $fields ) && $fields = array ( $fields );
        foreach ( $fields as $field ) {
            if ( $valid_empty ) {
                if ( ! isset ( $_POST[ $field ] ) || trim ( $_POST[ $field ] ) == '' || empty ( $_POST[ $field ] ) ) {
                    return true;
                }
            } else if ( ! isset ( $_POST[ $field ] ) || trim ( $_POST[ $field ] ) == '' ) {
                return true;
            }
        }
       return false;
    }

    public static function isGetFieldEmpty ( $fields, $valid_empty = true ) {
        ! is_array ( $fields ) && $fields = array ( $fields );
        foreach ( $fields as $field ) {
            if ( $valid_empty ) {
                if ( ! isset ( $_GET[ $field ] ) || trim ( $_GET[ $field ] ) == '' || empty ( $_GET[ $field ] ) ) {
                    return true;
                }
            } else {
                if ( ! isset ( $_GET[ $field ] ) || trim ( $_GET[ $field ] ) == '' ) {
                    return true;
                }
            }
        }
         return false;
    }

    public static function getFieldKeyIndex ( $fields, $value, $value_name = EnumAuditTrailField::html_name ) {
        $return = null;
        if ( is_array ( $fields ) )  {
            $count = count ( $fields );
            for ( $i = 0; $i < $count; $i++ ) {
                $field = $fields[$i];
                if ( strtolower ( $field[ $value_name ] ) == strtolower ( $value ) ) {
                    $return = $i;
                    break;
                }
            }
        }
        return $return;
    }

    public static function getExistRefPath ( $path, $kill_cache = false ) {
        $return = $path;
        if ( strlen ( $path ) <= 3 ) {
            return $path;
        }
        if ( substr ( $path, 0, 3 ) != '../' ) {
            if ( file_exists ( $path ) ) {
                $return = $path;
            } else if ( file_exists( '../' . $path ) ) {
                $return = '../' . $path;
            }
        } else {
            if ( file_exists ( $path ) ) {
                $return = $path;
            } else if ( file_exists( substr ( $path, 3, strlen ( $path ) ) ) ) {
                $return = substr ( $path, 3, strlen ( $path ) );
            }
        }
        $path = WEBROOT . $path;
        $kill_cache && $path .= '?lm=' . (string) filemtime ( $return );
        return $path;
    }
}
