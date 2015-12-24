<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 14:49
 */

namespace ReactHttp;


use React\Http\Request;
use React\Http\Response;
use ReactHttp\Exception\ReactHttpException;

class Route
{

    private static $me;
    private $controllers;


    private function __construct( ) {
        $this->controllers=array();
    }

    public static function getInstance( ) {
        if ( self::$me == null ) {
            self::$me = new Route();
        }
        return self::$me;
    }

    static function action($method, $path, $function) {
        $me = self::getInstance();
        $me->controllers[$method]= array($path => new RoutePath($path,$function));
        return $me;
    }

    function getPaths($path){
        $path_array = preg_split("/\//", $path);
        foreach ($path_array as $key => $path_element) {
            if (strlen(trim($path_element)) == 0) {
                unset($path_array[$key]);
            }
        }
        $path_array = array_values($path_array);

        return $path_array;
    }

    function checkPaths($route_path,$request_path,&$restInput) {
        $is_match = true;
        if(count($route_path)!=count($request_path)) {
            return false;
        } else {
            foreach ($route_path as $key => $path_element) {
                if (preg_match("/^\:(.*)$/", $path_element, $match)
                    || preg_match("/^\{(.*)\}$/", $path_element, $match)
                ) {
                    Input::set($match[1], $request_path[$key]);
                    $restInput[]=$request_path[$key];
                } else {
                    if (!isset($request_path[$key]) ||
                        $request_path[$key] != $path_element
                    ) {
                        $is_match=false;
                        break;
                    }
                }
            }
            return $is_match;
        }
    }

    public function perform( Request $request, Response $response)
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if (array_key_exists($method, $this->controllers)) {
            $controllers = $this->controllers[$method];

            $input_paths = $this->getPaths($path);
            /**
             * @var  RoutePath $pathObject
             */

            $target_controller = null;
            $restParams = array();

            foreach ($controllers as $route_path => $pathObject) {
                $restInput = array();
                $check = $this->checkPaths($pathObject->path, $input_paths, $restInput);

                if (!$check) {
                    continue;
                } else {
                    $target_controller = $pathObject->controller;
                    $restParams = $restInput;

                    break;
                }
            }

            if ($target_controller == null) {
                // not found
                HttpResponse::html($response,"URL not found!",400);

            } else {

                $inputs = array($request,$response);

                $inputs = array_merge($inputs,$restParams);

                if (is_callable($target_controller)) {
                    call_user_func_array($target_controller, $inputs);
                } else {
                    $function_array = preg_split("/@/", $target_controller);
                    if (!isset($function_array[1]))
                        throw ReactHttpException::internalError('Routing Error');

                    $class_name = $function_array[0];
                    $method_name = $function_array[1];

                    //$response = $class_name::$method_name();
                    // Initialization controller object
                    $controller = new $class_name;

                    call_user_func_array(array($controller, $method_name), $inputs);
                    //$response = $controller->$method_name();
                }
            }

        } else {
            // not found
            HttpResponse::html($response,"URL not found!",400);
        }

    }

    static function get($path, $function) {
        return self::action('GET', $path, $function);
    }
    static function post($path, $function) {
        return self::action('POST', $path, $function);
    }
    static function put($path, $function) {
        return self::action('PUT', $path, $function);
    }
    static function patch($path, $function) {
        return self::action('PATCH', $path, $function);
    }
    static function delete($path, $function) {
        return self::action('DELETE', $path, $function);
    }
}