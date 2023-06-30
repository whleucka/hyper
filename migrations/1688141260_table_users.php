<?php
namespace Nebula\Migrations;

use Nebula\Database\Blueprint;
use Nebula\Database\Migration;
use Nebula\Database\Schema;

return new class extends Migration {
    public function up(): string
    {
        return Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid");
            $table->varchar("name");
            $table->varchar("email");
            $table->binary("password", 96);
            $table->char('remember_token', 32);
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
