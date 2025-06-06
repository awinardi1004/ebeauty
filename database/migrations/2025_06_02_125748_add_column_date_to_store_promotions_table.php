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
        Schema::table('store_promotions', function (Blueprint $table) {
            $table->dateTime('start_date')->after('path');
            $table->dateTime('end_date')->after('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_promotions', function (Blueprint $table) {
            //
        });
    }
};
