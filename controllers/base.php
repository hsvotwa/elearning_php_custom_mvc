<?php
    class BaseController {
        public  $g_layout = 'default',  
                $g_form_action = "",
                $g_records = null,
                $g_record_id = null,
                $g_form_fields = null,
                $g_other_data = null,
                $g_can_edit = null,
                $g_variables = [];

        function set( $d ) {
            $this->g_variables = array_merge( $this->g_variables, $d );
        }

        function render( $file_name, $title = "", $return = false ) {
            $class = strtolower( str_replace( 'Controller', '', get_class( $this ) ) );
            if( ! in_array ( $file_name, array( "login", "loginfeedback", "logout", "welcome" ) ) &&
                ! ( new UserMdl( UserSessionMdl::getUuid() ) )->hasAccessTo( EnumUserRoleType::none ) ) {
                if ( ! UserSessionMdl::getUuid() ) {
                    ( new AccountController() )->login();
                    return;
                }
            }
            
            extract( $this->g_variables );
            ob_start();
            $gen = new GeneralDisplay();
            $records = $this->g_records;
            $form_fields = $this->g_form_fields;
            $form_action = $this->g_form_action;
            $record_id = $this->g_record_id;
            $other_data = $this->g_other_data;
            $can_edit = $this->g_can_edit;
            $page_title = $title;
            require( ROOT . "views/" . strtolower( str_replace( 'Controller', '', get_class( $this ) ) ) . '/' . strtolower( $file_name ) . '.php' );
            $content_for_layout = ob_get_clean();
            if ( ! $this->g_layout ) {
                if( ! $return ) {
                    echo $content_for_layout;
                } else {
                    return $content_for_layout;
                }
            } else {
                $css_import = $gen->getCss();
                $javascript_import = $gen->getJavascript();
                $page_html_title = $gen->getTitle( $title );
                $navigation = $gen->getNavigation();
                $app_name = APP_SHORT_NAME;
                $logo_path = APP_DOMAIN . "/images/logo.png";
                $loader_path = APP_DOMAIN . "/images/ball.gif";
                $selected_profile = APP_NAME;
                $user_detail = ! UserSessionMdl::getUuid() ?  "" : UserSessionMdl::getName() . " " . UserSessionMdl::getSurname();
                require( ROOT . "views/layouts/" . $this->g_layout . '.php' );
            }
        }

        private function secure_input( $data ) {
            $data = trim( $data );
            $data = stripslashes( $data );
            $data = htmlspecialchars( $data );
            return $data;
        }

        protected function secure_form( $form ) {
            foreach ( $form as $key => $value ) {
                $form[ $key ] = $this->secure_input( $value );
            }
        }
    }