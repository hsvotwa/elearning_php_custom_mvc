<?php
class RouteConfig {
    static public function parse( $url, $request ) {
        $url = trim( $url );
        $explode_url = explode( '/', $url );
        $explode_url = array_slice( $explode_url, Common::isLiveServer() ? 1 : 2 );
        $request->controller = $explode_url[ 0 ];
        if( sizeof( $explode_url ) < 2 ) {
            $explode_url[ 1 ] = "welcome";
        }
        $request->action = $explode_url[ 1 ];
        $request->params = array_slice( $explode_url, 2 );
    }
}
?>