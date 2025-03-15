<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تعديل طول الحقل باستخدام SQL
        DB::statement("ALTER TABLE users MODIFY country_code VARCHAR(50) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة الحقل لطوله السابق باستخدام SQL
        DB::statement("ALTER TABLE users MODIFY country_code VARCHAR(10) NULL");
    }
};
