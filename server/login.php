<?php
header("Content-Type: application/json; charset=utf-8");
require_once "vendor/autoload.php";
require_once "config.php";
use petitphotobox\controllers\LoginController;

$c = new LoginController();
$c->printResponse();
