<?php
header("Content-Type: application/json; charset=utf-8");
require_once "vendor/autoload.php";
use petitphotobox\LoginController;

$c = new LoginController();
// echo json_encode($c->getResponse());
