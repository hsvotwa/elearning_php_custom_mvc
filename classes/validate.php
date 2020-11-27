<?php
class Validate {
    private static function getMySql() {
        return new MySql();
    }

    public static function isUrl ( $value, $check_dnsrr = false, $check_dnsrr_only = false ) {
        $return = false;
        if ( ! $check_dnsrr_only ) {
            $return = preg_match(
                                    '#^( (http(s)?://)|(www\.) )[a-z0-9-]+(\.[a-z]{2,4})+(/)?#i',
                                    $value
                                );
        }
        if ( $return
                && $check_dnsrr
                || $check_dnsrr_only ) {
            $host = str_replace( array ( 'http://', 'https://', 'www.' ), '', strtolower ( $value ) );
            if ( strpos ( $host, '/' ) ) {
                $host = substr ( $host, 0, strpos ( $host, '/' ) );
            } else if ( strpos ( $host, '?' ) ) {
                $host = substr ( $host, 0, strpos ( $host, '?' ) );
            }
            $return = checkdnsrr( $host, 'A' ) || checkdnsrr( $host, 'MX' );
        }
        return $return;
    }
 
    public static function isEmail ( $value, $verify_domain = false ) {
        $value = trim ( $value );
        $return = filter_var ( $value, FILTER_VALIDATE_EMAIL );
        if ( $return
                && $verify_domain ) {
            $parts = explode( '@', $value );
            $domain = $parts[ count ( $parts ) - 1 ];
            $return = function_exists( 'getmxrr' ) ? getmxrr ( $domain, $mxhosts ) : @fsockopen( $domain, 25, $errno, $errstr, 5 );
        }
        return $return;
    }

    public static function inArray ( $needle, $haystack, &$get_index = null, $strict = false ) {
        is_string ( $needle ) && $needle = strtolower ( $needle );
        $i = 0;
        foreach ( $haystack as $value ) {
            if ( is_array ( $value ) ) {
                if ( self::inArray ( $needle, $value ) ) {
                    $get_index = $i;
                    return true;
                }
            } else {
                is_string ( $value ) && $value = strtolower ( $value );
                if ( ! $strict
                        && $needle == $value
                        || $needle === $value ) {
                    $get_index = $i;
                    return true;
                }
            }
            $i++;
        }
        return false;
    }

    public static function isNum( $value, $min = null, $max = null ) {
        ! is_array( $value ) && $value = [$value];
        foreach ( $value as $v ) {
            if ( ! is_numeric( $v ) ) {
                return false;
            }
            switch ( true ) {
                case self::isNum( [$min, $max] ): //not recursion
                    if ( ! self::isBetween( $v, $min, $max ) ) {
                        return false;
                    }
                    break;
                case is_numeric( $min ):
                    if ( $v < $min ) {
                        return false;
                    }
                    break;
                case is_numeric( $max ):
                    if ( $v > $max ) {
                        return false;
                    }
                    break;
            }
        }
        return true;
    }

    public static function isBetween( $value, $min, $max, $incl = true ) {
        if ( ! self::isNum( [$value, $min, $max] ) ) {
            return false;
        }
        return (
            $incl
            ? ( $value >= $min && $value <= $max )
            : ( $value > $min && $value < $max )
        );
    }

    public static function isLen( $value, $len, $strict = true ) {
        ! is_array( $value ) && $value = [$value];
        foreach ( $value as $v ) {
            if ( $strict ) {
                if ( ! isset( $v[$len - 1] ) || isset( $v[$len] ) ) {
                    return false;
                }
            } else if ( isset( $v[$len] ) ) {
                return false;
            }
        }
        return true;
    }
}

class Valid extends Validate {}
?>