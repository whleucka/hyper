<?php

namespace Nebula\Migrations;

use Nebula\Interfaces\Database\Migration;
use Nebula\Database\Blueprint;
use Nebula\Database\Schema;

/** users schema
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL DEFAULT (uuid()),
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` binary(96) DEFAULT NULL,
  `two_fa_secret` char(16) DEFAULT NULL,
  `remember_token` char(32) DEFAULT NULL,
  `reset_token` char(32) DEFAULT NULL,
  `reset_expires_at` bigint unsigned,
  `failed_login_attempts` tinyint unsigned default 0,
  `lock_expires_at` bigint unsigned,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
); 
 */

return new class extends Migration
{
    public function up(): string
    {
        return Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->default("(UUID())");
            $table->varchar("name");
            $table->varchar("email");
            $table->binary("password", 96);
            $table->char("two_fa_secret", 16)->nullable();
            $table->char('remember_token', 32)->nullable();
            $table->char('reset_token', 32)->nullable();
            $table->unsignedBigInteger('reset_expires_at')->nullable();
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->unsignedBigInteger('lock_expires_at')->nullable();
            $table->timestamps();
            $table->unique("email");
            $table->primaryKey("id");
        });
    }

    public function down(): string
    {
        return Schema::drop("users");
    }
};
