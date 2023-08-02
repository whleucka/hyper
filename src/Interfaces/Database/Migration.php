<?php

namespace Nebula\Interfaces\Database;

interface Migration
{
    public function up(): string;
    public function down(): string;
}
