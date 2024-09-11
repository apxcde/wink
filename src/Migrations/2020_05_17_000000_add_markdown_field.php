<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarkdownField extends Migration
{
    public function up(): void
    {
        Schema::table('wink_posts', function (Blueprint $table) {
            $table->boolean('markdown')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('wink_posts', function (Blueprint $table) {
            $table->boolean('markdown')->default(false);
        });
    }
}
