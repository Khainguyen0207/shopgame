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
        Schema::table('game_accounts', function (Blueprint $table) {
            $table->dropColumn('server');
            $table->dropColumn('planet');
            $table->dropColumn('earring');
            $table->dropColumn('account_name');
            $table->string('registration_type')->change();
            $table->string('username')->after('game_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_accounts', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('server')->nullable();
            $table->string('planet')->nullable();
            $table->string('earring')->nullable();
            $table->enum('registration_type', ['virtual','real'])->change();
            $table->string('account_name')->nullable();
        });
    }
};
