<?php
namespace petitphotobox\controllers;
use \Exception;
use petitphotobox\auth\User;
use petitphotobox\controller\BaseController;
use petitphotobox\exceptions\AuthException;

class UserLoginController extends BaseController
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
    $username = trim($this->getParam("username"));
    $password = trim($this->getParam("password"));

    User::login($username, $password);
  }
}