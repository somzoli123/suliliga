<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            // Az aktuálisan futó szakasz kezdete (NULL = nem fut: szünet vagy nem élő).
            $table->timestamp('live_started_at')->nullable()->after('scheduled_at');
            // A korábbi (már lezárt) szakaszokban eltelt másodpercek.
            $table->unsignedInteger('elapsed_seconds')->default(0)->after('live_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->dropColumn(['live_started_at', 'elapsed_seconds']);
        });
    }
};
