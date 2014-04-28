<?php

include('requests/Requests.php');
Requests::register_autoloader();

$direct_print_code = 'KPVDJK8XUQJU';

$html = 'html=<html><head><meta charset="utf-8"></head><body><h1>hey'.$obj->{$data->name}.'</h1></body></html>';

error_log("Error message\n", 3, "../logs/error_log");

$base_url = 'http://remote.bergcloud.com/playground/direct_print/';

$response = Requests::post($base_url.$direct_print_code, null, $html);
echo 'done';
