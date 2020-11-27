<?php
class Convert {
    public static function toNumber ( $value, $to_sql = false , $decimal = null, $zero_desc = '-' ) {
        $value = str_replace(
                        array ( ',', ' ' ),
                        '',
                        self::trimSpace ( $value, '' )
                    );
        if ( $to_sql ) {
            return $value;
        }
        if ( trim ( (string) $value ) == '' ) {
        	return '';
        }
        is_null ( $decimal ) && $decimal = 2;
        $value = round ( $value, $decimal );
        $values = explode( '.', $value );
        $value = number_format ( $values[0] );
        if ( $value == '0' && substr ( $values[0], 0, 1 ) == '-' )  {
            $value = '-' . $value;
        }
        if ( $decimal != 0 ) {
            $value .= '.';
        }
        if ( count ( $values ) == 2 ) {
            $value .= str_pad ( $values[1], $decimal, '0', STR_PAD_RIGHT );
        } else {
            $value .= str_pad ( '', $decimal, '0', STR_PAD_RIGHT );
        }
        $value == '0.' . str_pad ( '', $decimal, '0', STR_PAD_RIGHT ) && $value = $zero_desc;
        return $value;
    }

    public static function toNum ( $value, $to_sql = false , $decimal = null, $zero_desc = '-' ) {
        return self::toNumber ( $value, $to_sql, $decimal, $zero_desc );
    }

    public static function toString ( $value, $to_sql = false ) {
        if ( ! $to_sql ) {
            return $value;
        }
        $mysql = new MySql();
        $con = $mysql->openConnection();
        $content = mysqli_real_escape_string ( $con, stripslashes ( strip_tags ( $value ) ) );
        return trim( $content );
    }

    public static function trimSpace ( $value, $replace = ' ' ) {
        return trim( preg_replace(
                                '/\s{3,}/',
                                $replace,
                                $value
                            ) );
    }

    public static function toCamelCase ( $value ) {
        return ucwords( strtolower( self::trimSpace ( $value ) ) );
    }

    public static function toDate ( $value, $to_sql = false, $date_only = false, $time_only = false ) {
        $value = self::getdate ( $value, $date_only );
        if ( ! $to_sql ) {
            if ( ! $date_only
                    && ! $time_only ) {
                return date ( USER_DATE_TIME_FORMAT, $value );
            }
            if ( $date_only ) {
                return date ( USER_DATE_FORMAT, $value );
            }
            return date ( USER_TIME_FORMAT, $value );
        }
        if ( ! $date_only
                && ! $time_only ) {
            return date ( DATETIME_MYSQL_FORMAT, $value );
        }
        if ( $date_only ) {
            return date ( DATE_MYSQL_FORMAT, $value );
        }
        return date ( TIME_MYSQL_FORMAT, $value );
    }

    public static function getdate ( $value, $date_only = false ) {
        is_string ( $value ) && $value = strtotime( self::replaceDateSep ( $value ) );
        if ( $date_only ) {
            $value = mktime(
                        0,
                        0,
                        0,
                        (int) date( 'n', $value ),
                        (int) date( 'j', $value ),
                        (int) date( 'Y', $value )
                    );
        }
        return $value;
    }

    private static function replaceDateSep( $value ) {
        if ( is_string ( $value ) )  {
           $searches = array ('/', '.' );  //All possible date seperators.
           return str_replace( $searches, '-', $value );
        } else {
            return $value;
        }
    }

     public static function concat ( &$value, $values, $trim = false, $reset = false, $escape = null ) {
        if ( $reset && '' !== $value ) {
            $value = '';
        }
        ! is_array ( $values ) && $values = [$values];
        if ( $trim ) {
            $values = array_map( function ( $v ) {
                return trim ( $v );
            }, $values );
        }
        $value .= implode ( '', $values );
        if ( isset ( $escape ) ) {
            true === $escape && $escape = "'";
            $value = str_replace ( $escape, '\\' . $escape, $value );
        }
        return $value;
    }

   public static function deterAutoloadPath( $in, &$out ) {
        '' !== $out && $out = '';
        $len = strlen( $in );
        $in_lower = strtolower( $in );
        $chars = array_flip( range( 'A', 'Z' ) );
        for ( $i = 0 ; $i < $len ; $i++ ) {
            if ( isset( $chars[$in[$i]] ) && '' !== $out ) {
                $out .= '_';
            }
            $out .= $in_lower[$i];
        }
        return ( '' !== $out );
    }

    public static function deterResp( &$result, $data, $url, $post = false, $try = 0, $try_max = 3 ) {
        isset( $result ) && $result = null;
        if ( ! $post ) { //handle get request
            $result = file_get_contents( "{$url}?{$data}" ); //so much faster than curl
            if ( ! empty( $result ) ) {
                return true;
            }
            return (
                ++$try <= $try_max
                ? self::deterResp( $result, $data, $url, $post, $try, $try_max ) //recursion
                : false
            );
        }
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
            'Host: ' . HOST_TIME,
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen( $data )
        ]);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec( $ch );
        curl_close( $ch );
        if ( ! empty( $result ) ) {
            return true;
        }
        return (
            ++$try <= $try_max
            ? self::deterResp( $result, $data, $url, $post, $try, $try_max ) //recursion
            : false
        );
    }

    public static function deterCryptRandCode( &$value, $len, $strict = false, $rm_common = true ) {
        '' !== $value && $value = '';
        if ( ! Validate::isNum( $len, /*$min = */1 ) ) {
            return false;
        } else if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
            return false;
        }
        $value = base64_encode( openssl_random_pseudo_bytes( $len ) );
        if ( $rm_common ) {
            $value = str_replace( ['/', '+', '='], '', $value );
        }
        if ( ! $strict ) {
            return ( '' !== $value );
        }
        $value = substr( $value, 0, $len );
        return Validate::isLen( $value, $len );
    }

    public static function deterCryptRandNum( &$value, $min, $max, $max_rep = 2 ) {
        isset( $value ) && $value = null;
        if ( ! Validate::isNum( [$min, $max], /*$min = */1 ) ) {
            return false;
        } else if ( ( $diff = ( $max - $min ) ) <= 0 ) {
            return false;
        }
        $range = ( $diff + 1 ); // because $max is inclusive
        $bit = ceil( log( $range, 2 ) );
        $byte = ceil( $bit / 8.0 );
        $bit_max = ( 1 << $bit );
        do {
            $num = hexdec( bin2hex( openssl_random_pseudo_bytes( $byte ) ) ) % $bit_max;
            if ( $num >= $range ) {
                continue;
            }
            if ( Validate::isLen( ( string )$max, /*$len = */10, /*$strict = */false ) && Validate::isNum( $max_rep, /*$min = */1 ) ) {
                $value_str = ( string )( $num + $min );
                $len = strlen( $value_str );
                $values = [];
                for ( $i = 0 ; $i < $len ; $i++ ) {
                    $values[] = substr( $value_str, $i, 1 );
                }
                $values = array_filter( array_count_values( $values ), function( $a ) use ( $max_rep ) {
                    return ( $a > $max_rep );
                });
                if ( ! empty( $values ) ) {
                    continue;
                }
            }
            break;
        } while ( true );
        return Validate::isBetween( $value = ( $num + $min ), $min, $max );
    }
}