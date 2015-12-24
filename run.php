<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 15:32
 */

require 'vendor/autoload.php';
require "example/route.php";

$app = function (\React\Http\Request $request,\React\Http\Response $response) {
    $request->on("data",function($data) use ($request,$response) {
        try {

            if($request->getMethod()!="GET") {
                $json_data = json_decode($data);
            } else {
                $json_data=new stdClass();
            }

            if($json_data!=null && $json_data!=false) {
                \ReactHttp\Route::getInstance()->perform($request,$response,$json_data);
            } else {
                // throw exeption about data is wrong
                throw new \ReactHttp\Exception\ReactHttpException("Data is not follow Json format");
            }

        } catch (Exception $e) {
            \ReactHttp\HttpResponse::html($response,$e->getMessage(),$e->getCode());
        }
    });

};

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket, $loop);

$http->on('request', $app);

$port = 8080;
$host = "127.0.0.1";

echo "Server running at http://{$host}:{$port}\n";

$socket->listen($port,$host);
$loop->run();

function debug($strString, $exit = false) {
    print '<pre>';
    print_r($strString);
    print '</pre>';
    if($exit) exit();
}