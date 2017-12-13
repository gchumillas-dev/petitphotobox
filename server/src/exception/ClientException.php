<?php
namespace petitphotobox\exception;
use \Exception;

/**
 * A `ClientException` represents an exception caused by the client.
 *
 * For example, when the user fills a form and do not complete the required
 * fields the system throws a `ClientException`. Same happens when the user
 * enters invalid credentials from the login form. All those exceptions are
 * caused by the interaction of the user with the application and not for the
 * application itself.
 */
class ClientException extends Exception
{

}
