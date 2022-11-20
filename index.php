<?php

ob_start();

#autoload
require ('vendor/autoload.php');

#carico costanti
require_once ('app/src/core.php');
require_once ('app/src/constants.php');

set_exception_handler( "ExceptionHandler" );

$RequestHandler = new App\src\Objects\RequestHandler();
$RequestHandler->call();