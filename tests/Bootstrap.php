<?php
use JsonRpcTests\ServiceManagerGrabber;

error_reporting(E_ALL | E_STRICT);

// Assume we use composer
$loader = require_once  '../vendor/autoload.php';
$loader->add("JsonRpcTests\\", __DIR__);

$loader->register();

ServiceManagerGrabber::setServiceConfig(require_once './application.config.php');
ob_start();