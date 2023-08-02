<?php

namespace Nebula\Migrations;

use Nebula\Interfaces\Database\Migration;
use Nebula\Database\Blueprint;
use Nebula\Database\Schema;

/**

Audit table = record changes

CREATE TABLE audit (
  id bigint unsigned not null auto_increment,
  user_id bigint unsigned,
  table_name varchar(255) not null,
  table_id varchar(255) not null,
  field varchar(255) not null,
  old_value text,
  new_value text,
  message text,
  created_at datetime not null default CURRENT_TIMESTAMP,
  primary key (id),
  foreign key (user_id) REFERENCES users (id) ON DELETE SET NULL
)

 */

return new class extends Migration
{
  public function up(): string
  {
    return Schema::create("audit", function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger("user_id")->nullable();
      $table->varchar("table_name");
      $table->varchar("table_id");
      $table->varchar("field");
      $table->text("old_value")->nullable();
      $table->text("new_value")->nullable();
      $table->text("message")->nullable();
      $table->dateTime("created_at")->default("CURRENT_TIMESTAMP");
      $table->primaryKey("id");
      $table->foreignKey("user_id")
        ->references("users", "id")
        ->onDelete("SET NULL");
    });
  }

  public function down(): string
  {
    return Schema::drop("audit");
  }
};
