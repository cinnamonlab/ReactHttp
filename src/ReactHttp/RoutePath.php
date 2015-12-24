<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 16:02
 */

namespace ReactHttp;


class RoutePath
{
    public $path;
    public $controller;

    public function __construct($route_path,$controller)
    {
        $this->path = $this->getPaths($route_path);
        $this->controller = $controller;
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


}