<?php
namespace petitphotobox\controllers;
use petitphotobox\core\controller\AuthController;
use petitphotobox\core\exception\ClientException;
use petitphotobox\core\model\Document;
use soloproyectos\text\Text;

class UserRegisterCompleteController extends AuthController
{
  /**
   * Creates a new instance.
   */
  public function __construct()
  {
    parent::__construct();
    $this->addPostRequestHandler([$this, "onPostRequest"]);
  }

  /**
   * {@inheritdoc}
   *
   * @return Document
   */
  public function getDocument()
  {
    // TODO: language should be in the database
    return new Document(
      [
        "name" => $this->user->name,
        "language" => $this->getCookie("lang")
      ]
    );
  }

  /**
   * Processes POST requests.
   *
   * @return void
   */
  public function onPostRequest()
  {
    $name = $this->getParam("name");
    $language = $this->getParam("language");

    if (Text::isEmpty($name) || Text::isEmpty($language)) {
      throw new ClientException("requiredFields");
    }

    $this->user->name = $name;
    $this->user->save();

    $this->setCookie("lang", $language);
  }
}