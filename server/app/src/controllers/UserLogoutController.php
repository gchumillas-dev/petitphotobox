<?php
namespace petitphotobox\controllers;
use petitphotobox\controller\AuthController;

class UserLogoutController extends AuthController
{

  /**
   * Creates a new instance.
   */
  public function __construct()
  {
    parent::__construct();
    $this->on("POST", [$this, "onPost"]);
  }

  /**
   * Processes POST requests.
   *
   * @return void
   */
  public function onPost()
  {
    $this->user->logout();
  }
}