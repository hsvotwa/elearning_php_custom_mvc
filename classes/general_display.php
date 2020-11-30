<?php
class GeneralDisplay {
    private $g_mysql = null;

    public function getMySql() {
        if ( is_null ( $this->g_mysql ) ) {
            $this->g_mysql = new MySql();
        }
        return $this->g_mysql;
    }

    public function getTitle ( $title = '' ) {
        $echo = '<title>';
        if ( $title != '' ) {
            $echo .=  $title;
        } else {
            $echo .= APP_NAME;
        }
        $echo .= '</title>';
        return $echo;
    }

    public function getCss() {
        $echo = $this->getCssRef( 'css/site.css' );
        $echo .= $this->getCssRef('css/vendor/jquery-ui.css');
        $echo .= $this->getCssRef('css/vendor/toastr.min.css');
        $echo .= '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700" />';
        return $echo;
    }

    private function getCssRef ( $path ) {
        return '<link rel="stylesheet" type="text/css" href="' . Common::getExistRefPath ( $path, true ) . '">';
    }

    public function getJavascript() {
        $echo = $this->getJavascriptRef ( 'js/vendor/jquery-3.3.1-min.js' );
        $echo .= '<script type="text/javascript">';
        $echo .= '$.ajaxSetup({ cache: false } );';
        $echo .= '</script>';
        $echo .= $this->getJavascriptRef ( 'js/vendor/jquery-ui.min.js' );
        if (Common::isLiveServer()) {
            $echo .= $this->getJavascriptRef ( 'js/common.js' );
        } else {
            $echo .= $this->getJavascriptRef ( 'js/common.js' );
        }
        $echo .= $this->getJavascriptRef ( 'js/vendor/jquery.validate.min.js' );
        $echo .= $this->getJavascriptRef ( 'js/vendor/toastr.min.js' );
        return $echo;
    }

    public function getJavascriptRef ( $path ) {
        return '<script language="JavaScript" type="text/javascript" src="' . Common::getExistRefPath ( $path, true ) . '"></script>';
    }

    public function getNavigation() {
        $query = "select * from tbl_lu_nav order by sequence;";
        $result = $this->getMySql()->getQryRlt ( $query );
        if ( ! $result || ! mysqli_num_rows ( $result ) ) { 
            return ""; 
        }
        $nav_echo = '<div class="div_nav">';
        while ( $row = mysqli_fetch_assoc ( $result ) ) {
            if ( ( new UserMdl( UserSessionMdl::getUuid() ) )->hasAccessTo( $row["user_type_id"] ) ) {
                $nav_echo .= '<a href="' . APP_DOMAIN . $row['controller'] . '/' . $row["action"] . '">' . $row['name'] . '</a>';
            }
        }
        if( new UserMdl( UserSessionMdl::getUuid() ) ) {
            $nav_echo .= '<a href="account/logout">Log out</a>'; 
        } else {
            $nav_echo .= '<a href="account/login">Log in</a>'; 
        }
        $nav_echo .= '</div>';
        return $nav_echo;
    }

    public function getHtmlLabel( $description, $is_form = true ) {
        $indicator = $is_form ? ":" : "";
        return "<label class=\"form_label\">$description$indicator</label>";
    }

    public function getHtmlDisplay( $object, $field_name, $ref_table, $ref_column) {
        $value = $object && isset( $object[ $field_name ] ) ? $object[ $field_name ] : ""; 
        if( $ref_table ) {
            $this->resolveValue( $value, $ref_table, $ref_column );
        }
        return "<font>$value</font>";
    }

    private function resolveValue( &$value, $ref_table, $ref_column ) {
        $query = "select name from $ref_table where $ref_column = '$value'";
        $result = ( new MySql() )->getQryRlt( $query );
        if( ! $result || ! $result->num_rows ) {
            return;
        }
        $value = mysqli_fetch_assoc( $result )["name"];
    }

    public function deterFieldHtml( $field ) {
        $echo_js = '';
        $value = $field->g_value_object && isset( $field->g_value_object[ $field->g_mysql_name ] ) ? $field->g_value_object[ $field->g_mysql_name ] : "";
        if( $field->g_target_field_type == EnumFieldType::_date && $value ) {
            $value = Convert::toDate(  $value, false, true); 
        }
        $echo = "";
        if ( $field->g_target_field_type == EnumFieldType::_checkbox ) {
            $echo .= "<label class=\"checkbox_label\">";
        } 
        $min_length_html = "";
        if( $field->g_min_length ) {
            $min_length_html = ' minlength="' . $field->g_min_length . '" ';
        }
        if( $field->g_compulsory && $field->g_target_field_type == EnumFieldType::_integer ) {
            $min_length_html = ' min="' . $field->g_min_length . '" ';
        }
        $echo .= '<input type=';
        if ( ! in_array ( $field->g_target_field_type, array ( EnumFieldType::_select, EnumFieldType::_textarea, EnumFieldType::_radiobutton ) ) ) {
            switch ( $field->g_target_field_type ) {
                case EnumFieldType::_date:
                case EnumFieldType::_string:
                case EnumFieldType::_datetime:
                    $echo .= '"text"';
                    break;
                case EnumFieldType::_double:
                case EnumFieldType::_float:
                case EnumFieldType::_integer:
                    $echo .= '"number"';
                    break;
                case EnumFieldType::_email:
                    $echo .= '"email"';
                    break;
                case EnumFieldType::_password:
                    $echo .= '"password"';
                    break;
                case EnumFieldType::_hidden:
                    $echo .= '"hidden"';
                    break;
                case EnumFieldType::_checkbox:
                    $echo .= '"checkbox"';
                    break;
                case EnumFieldType::_file:
                    $echo .= '"file"';
            }
            if ( $field->g_target_field_type == EnumFieldType::_date ) {
                $echo_js .= '<script>';
                $echo_js .= '$(function() {';
                $echo_js .= "$('#" . $field->g_html_name . "' ).datepicker({ yearRange: '-100:+10', dateFormat: 'dd/mm/yy', " .
                                " showButtonPanel: true,
                                changeMonth: true,
                                changeYear: true,
                                showOtherMonths: true,
                                selectOtherMonths: true } );";
                $echo_js .= '} );';
                $echo_js .= '</script>';
            } 
            if ( in_array ( $field->g_target_field_type, array ( EnumFieldType::_double, EnumFieldType::_float ) )  ) {
                $echo_js .= '<script>';
                $echo_js .= '$(function() {';
                $echo_js .= "$('#" . $field->g_html_name . "' ).blur(function() {";
                $echo_js .= "if ( validateNum('$field->g_html_name' ) ) {";
                $echo_js .= "$('#" . $field->g_html_name . "' )";
                $echo_js .= ".removeClass(getInputClass('$field->g_html_name', false) )";
                $echo_js .= ".addClass(getInputClass('$field->g_html_name', true) );";
                $echo_js .= "$('#" . $field->g_html_name . "' ).formatCurrency({";
                $echo_js .= "symbol : '',";
                $echo_js .= "negativeFormat : '-%s%n',";
                $echo_js .= "roundToDecimalPlace : $field->g_decimal_place_count";
                $echo_js .= '} );';
                $echo_js .= "} else {";
                $echo_js .= "$('#" . $field->g_html_name . "' )";
                $echo_js .= ".removeClass(getInputClass('$field->g_html_name', true) )";
                $echo_js .= ".addClass(getInputClass('$field->g_html_name', false) );";
                $echo_js .= "}";
                $echo_js .= '} );';
                $echo_js .= '} );';
                $echo_js .= '</script>';
            }
            $comp_indicator = $field->g_compulsory ? " *" : "";
            $echo .= " name=\"$field->g_html_name\" id=\"$field->g_html_name\"";
            if ( $field->g_target_field_type != EnumFieldType::_checkbox ) {
                $echo .= ( trim( $value) != '' ? ' value="' . htmlentities( $value ) . '"' : '' );
            }
            if ( $field->g_target_field_type == EnumFieldType::_file ) {
                $echo .= ' accept=".xls,.xlsx,.pdf,.doc,.docx,image/x-png,image/jpeg" ';
            }
            $echo .= $min_length_html;
            $echo .= ( trim( $field->g_css_class ) != '' ? ' class="' . $field->g_css_class . '"' : '' );
            if( $field->g_target_field_type != EnumFieldType::_hidden ) {
                $echo .= " placeholder=\"$field->g_description$comp_indicator\"";
            }
            $echo .= $field->g_compulsory ? " required=\"required\" " : "";
            if ( $field->g_target_field_type == EnumFieldType::_checkbox && $value ) {
                $echo .=  " checked ";
            }
            $echo .= ' />';
            if ( $field->g_target_field_type == EnumFieldType::_checkbox ) {
                $echo .=  $field->g_description . "</label>";
            } 
        } 
        if ( $field->g_target_field_type == EnumFieldType::_select ) {
            $echo = '<select';
            $echo .= " name=\"$field->g_html_name\" id=\"$field->g_html_name\"";
            $echo .= ( $value != '' ? ' value="' . $value . '"' : '' );
            $echo .= ( $field->g_css_class != '' ? ' class="' . $field->g_css_class . '"' : '' );
            $echo .= '>';
            if ( ! is_null ( $field->g_list_default) ) {
                $echo .= '<option value="">';
                $echo .= $field->g_list_default;
                $echo .= '</option>';
            }
            if ( ! is_null ( $field->g_list_source ) ) {
                if ( ! is_array ( $field->g_list_source ) && $field->g_list_source ) {
                    while ( $row = mysqli_fetch_assoc ( $field->g_list_source ) ) {
                        $echo .= '<option value="' . (string) $row['value'] . '"' . ( $row['value'] != $value ? '' : ' selected' ) . '>';
                        $echo .= $row['name'];
                        $echo .= '</option>';
                    }
                    if ( ! is_null ( $field->g_list_source ) ) {
                        mysqli_data_seek( $field->g_list_source, 0 );
                    }
                } else if ( ! is_null ( $field->g_list_source ) ) {
                    foreach ( $field->g_list_source as $option ) {
                        $echo .= '<option value="' . (string) $option['value'] . '"' . ( $option['value'] != $value ? '' : ' selected' ) . '>';
                        $echo .= $option['name'];
                        $echo .= '</option>';
                    }
                }
            }
            $echo .= '</select>';
        } 
        if ( $field->g_target_field_type == EnumFieldType::_radiobutton ) {
            $echo = "<div>";
            if ( ! is_null ( $field->g_list_source ) ) {
                if ( ! is_array ( $field->g_list_source ) && $field->g_list_source ) {
                    while ( $row = mysqli_fetch_assoc ( $field->g_list_source ) ) {
                        $echo .= "<label class=\"no_italic\" for=\"" . $row['value'] . "\"";
                        $echo .= ( $field->g_css_class != '' ? ' class="' . $field->g_css_class . '"' : '' ) . ">";
                        $echo .= '<input type="radio"';
                        $echo .= " name=\"$field->g_html_name\" id=\"" . $row['value'] . "\"";
                        $echo .= ' value="' . ( string ) $row['value'] . '"';
                        $echo .= ( $row['value'] != $value ? '' : ' checked ' );
                        $echo .= '/>';
                        $echo .= $row['name'];
                        $echo .= '</label>';
                    }
                    if ( ! is_null ( $field->g_list_source ) ) {
                        mysqli_data_seek( $field->g_list_source, 0 );
                    }
                } else {
                    foreach ( $field->g_list_source as $option ) {
                        $echo .= "<label class=\"no_italic\" for=\"" . $row['value'] . "\">";
                        $echo .= ( $field->g_css_class != '' ? ' class="' . $field->g_css_class . '"' : '' ) . ">";
                        $echo .= '<input type="radio"';
                        $echo .= " name=\"$field->g_html_name\" id=\"" . $row['value'] . "\"";
                        $echo .= ' value="' . (string) $row['value'] . '"';
                        $echo .= ( $row['value'] != $value ? '' : ' checked ' );
                        $echo .= '/>';
                        $echo .= $row['name'];
                        $echo .= '</label>';
                    }
                }
            }
            $echo .= "</div>";
        }
        if (  $field->g_target_field_type == EnumFieldType::_textarea ) {
            $echo = '<textarea';
            $echo .= " name=\"$field->g_html_name\" id=\"$field->g_html_name\"";
            $echo .= ( $field->g_css_class != '' ? ' class="' . $field->g_css_class . '"' : '' );
            $echo .= '>';
            if ( trim( $value ) != '' ){
                $echo .= trim( $value );
            }
            $echo .= '</textarea>';
        }
        return $echo . $echo_js;
    }

    public function getContUuid( $is_id = false, $table_name = '', $name = "uuid" ) {
         if ( ! Common::isGetFieldEmpty ( $name ) ) {
             if ( ! $is_id ) {
                 return (string) $_GET[$name];
             }
             $query = "select uuid
                            from $table_name
                            where id = " . $_GET[$name];
             $row = mysqli_fetch_assoc ( $this->getMySql()->getQryRlt ( $query ) );
             if ( $row ) {
                 return $row['uuid'];
             }
        } else if ( ! Common::isPostFieldEmpty ( $name) ) {
            return (string) $_POST[$name];
        } else {
            return $this->getMySql()->getUuid();
        }
        return '';
     }

     public function getLookUpVal( $table_name, $lkp_val, $lkp_col = 'enum_id', $get_col = 'name' ) {
       $query = "select " . $get_col ." as value
                               from " . $table_name . "
                               where " .  $lkp_col . " = '" . $lkp_val . "'";
       $row = mysqli_fetch_assoc ( $this->getMySql()->getQryRlt ( $query ) );
       if ( $row ) {
           return $row['value'];
       }
       return '';
   }

   function deterFeedback( $success, $new_title, $messages = null ) {
        $data = array();
        $data["success"] = $success;
        $data["title"] = $new_title;
        if( ! is_null( $messages ) && $messages ) {
            if( ! is_array( $messages ) && $messages != "" ) {
                $data["message"] = $messages;
            } else if ( ! empty ( $messages ) ) {
                $tmp_mess = "";
                $tmp_mess .= "<ul>";
                foreach ( $messages as $value ) {
                    $tmp_mess .= '<li>' . $value . '</li>';
                }
                $tmp_mess .= '</ul>';
                $data["message"] = $tmp_mess;
            }
        } else if( ! $success ) {
            $data["message"] = "Data was not saved.";
        } else {
            $data["message"] = "Data successfully saved.";
        }
        return json_encode( $data );
   }
}