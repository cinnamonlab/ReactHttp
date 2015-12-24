<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 15:34
 */
use ReactHttp\Route;

Route::action("GET","/users/{id}/detail",function($request,$response,$xxx) {
    \ReactHttp\HttpResponse::html($response,"Welcome ".$xxx,200);
});
Route::action("PUT","/users/{id}/detail",function($request,$response,$xxx) {
    \ReactHttp\HttpResponse::html($response,"Welcome ".$xxx,200);
});
Route::action("POST","/users/{id}/detail",function($request,$response,$xxx) {
    \ReactHttp\HttpResponse::html($response,"Welcome ".$xxx,200);
});