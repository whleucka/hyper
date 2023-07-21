<?php

namespace Nebula\Session;

class Flash
{
  private static $key = 'flash';

  public static function addMessage($type, $message)
  {
    $messages = session()->get(self::$key);
    $messages[] = ['type' => $type, 'message' => $message, 'ts' => date('Y-m-d H:i:s')];
    session()->set(self::$key, $messages);
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
