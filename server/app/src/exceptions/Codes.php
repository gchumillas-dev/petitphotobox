<?php
// TODO: eliminar este archivo
namespace petitphotobox\exceptions;

/**
 * List of exception codes.
 *
 * An `exception` is caused by the client whereas an `error` is caused by
 * the application. For example, if the user enters invalid credentials it
 * throws an `AuthException`. A database connection issue would
 * throw a `DbError`.
 */
class Codes
{
  // list of client exceptions
  const AUTH_EXCEPTION = 401;

  // list of application exceptions
  const DB_ERROR = 501;
  const SESSION_ERROR = 502;
}
