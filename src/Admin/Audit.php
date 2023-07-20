<?php

namespace Nebula\Admin;

class Audit
{
  public static function insert(?int $user_id, string $table_name, string $table_id, string $field, string $old_value = null, string $new_value = null, string $message = null): mixed
  {
    return db()
      ->query("INSERT INTO audit (user_id, table_name, table_id, 
        field, old_value, new_value, message) 
        VALUES (?,?,?,?,?,?,?)", ...[
        $user_id,
        $table_name,
        $table_id,
        $field,
        $old_value,
        $new_value,
        $message
      ]);
  }
}
