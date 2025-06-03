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
        Schema::table('product_promotion_details', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->after('product_promotion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_promotion_details', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['product_id']);
            // Baru hapus kolom
            $table->dropColumn('product_id');
        });
    }
};
