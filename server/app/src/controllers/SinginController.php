<?php
namespace petitphotobox\controllers;
use petitphotobox\auth\User;
use petitphotobox\controller\BaseController;
use petitphotobox\exception\ClientException;
use petitphotobox\exceptions\AuthException;
use soloproyectos\text\Text;

class SinginController extends BaseController
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
    $rePassword = trim($this->getParam("re_password"));

    if (   Text::isEmpty($username)
        || Text::isEmpty($password)
        || Text::isEmpty($rePassword)
    ) {
      throw new ClientException(
        "The following fields are required: username, password, re_password"
      );
    }

    $user = User::searchByName($username);
    if ($user !== null) {
      throw new ClientException("The user already exist");
    }

    if (strlen($password) < MIN_PASSWORD_LENGTH) {
      throw new ClientException(
        "Password must have at least " . MIN_PASSWORD_LENGTH . " characters"
      );
    }

    if ($password !== $rePassword) {
      throw new ClientException("Passwords do not match");
    }

    $user = User::create($username, $password);
    User::login($username, $password);
  }
}
