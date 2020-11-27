<?php
class Dispatcher {
    private $request;

    public function dispatch() {
        $this->request = new Request();
        RouteConfig::parse( urldecode( $this->request->url ), $this->request );
        $controller = $this->loadController();
        if( is_callable( [ $controller, $this->request->action ] ) ) {
            call_user_func_array( [ $controller, $this->request->action ], $this->request->params );
        } else {
            $controller = new ErrorController();
            call_user_func_array( [ $controller, "Error404" ], $this->request->params );
        }
    }

    public function loadController() {
        $name = $this->request->controller;
        if( ! $name ) {
            $name = "home";
        }
        $file = ROOT . 'controllers/' . $name . '.php';
        if( ! file_exists( $file ) ) {
            return new ErrorController();
        }
        // require( $file );
        $name = ucfirst( $name ) . "Controller";
        $controller = new $name();
        return $controller;
    }
}