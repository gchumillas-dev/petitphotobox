<?php
namespace petitphotobox\controllers;
use petitphotobox\core\auth\UserAuth;
use petitphotobox\core\controller\BaseController;
use petitphotobox\core\exception\ClientException;
use soloproyectos\text\Text;
use petitphotobox\models\UserLoginModel;
use petitphotobox\documents\UserLoginDocument;

class UserLoginController extends BaseController
{
  private $_document;

  /**
   * Creates a new instance.
   */
  public function __construct()
  {
    parent::__construct();
    $this->_document = new UserLoginDocument();
    $this->addOpenRequestHandler([$this, "onOpenRequest"]);
    $this->addPostRequestHandler([$this, "onPostRequest"]);
  }

  public function getDocument()
  {
    return $this->_document;
  }

  /**
   * Processes POST requests.
   *
   * @return void
   */
  public function onOpenRequest()
  {
    $this->_document->setUsername($this->getParam("username"));
    $this->_document->setPassword($this->getParam("password"));
  }

  /**
   * Processes POST requests.
   *
   * @return void
   */
  public function onPostRequest()
  {
    $username = $this->_document->getUsername();
    $password = $this->_document->getPassword();

    if (Text::isEmpty($username) || Text::isEmpty($password)) {
      throw new ClientException("Required fields: username, password");
    }

    UserAuth::login($username, $password);
  }
}
