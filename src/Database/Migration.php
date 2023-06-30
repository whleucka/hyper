<?php

namespace Nebula\Database;

abstract class Migration
{
    abstract public function up(): string;
    abstract public function down(): string;
}
