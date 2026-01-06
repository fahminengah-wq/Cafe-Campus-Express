<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // All columns already exist from create migration
        // This migration is redundant
    }

    public function down(): void
    {
        // No changes to revert
    }
};
