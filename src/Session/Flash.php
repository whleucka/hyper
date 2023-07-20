<?php

namespace Nebula\Session;

class Flash
{
  private static $key = 'flash';

  public static function addMessage($type, $message)
  {
    session()->set(self::$key, ['type' => $type, 'message' => $message]);
  }

  public static function getMessages()
  {
    $messages = session()->get(self::$key);
    // Clear the messages from the session once retrieved
    session()->set(self::$key, null);
    return $messages;
  }

  public static function hasMessages()
  {
    return is_null(session()->get(self::$key)) || !empty(session()->get(self::$key));
  }
}
