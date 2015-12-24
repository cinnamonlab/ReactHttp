<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 15:32
 */

require 'vendor/autoload.php';
require "example/route.php";

$i = 0;
$app = function ($request, $response) use ($i) {
    try {
        \ReactHttp\Route::getInstance()->perform($request,$response);
    } catch (Exception $e) {
        debug($e->getTraceAsString());
    }
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