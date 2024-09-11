<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaFields extends Migration
{
    public function up(): void
    {
        Schema::table('wink_tags', function (Blueprint $table) {
            $table->text('meta')->nullable();
        });

        Schema::table('wink_pages', function (Blueprint $table) {
            $table->text('meta')->nullable();
        });

        Schema::table('wink_authors', function (Blueprint $table) {
            $table->text('meta')->nullable();
        });

        Schema::table('wink_posts', function (Blueprint $table) {
            $table->text('meta')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('wink_tags', function (Blueprint $table) {
            $table->dropColumn('meta');
        });

        Schema::table('wink_pages', function (Blueprint $table) {
            $table->dropColumn('meta');
        });

        Schema::table('wink_authors', function (Blueprint $table) {
            $table->dropColumn('meta');
        });

        Schema::table('wink_posts', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
}
