<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anisth_business', function (Blueprint $table) {
            $table->dropUnique(['phone']); // Remove the unique constraint from phone
            $table->unique(['country_code', 'phone']); // Add composite unique constraint
        });
    }

    public function down(): void
    {
        Schema::table('anisth_business', function (Blueprint $table) {
            $table->dropUnique(['country_code', 'phone']); // Remove composite unique constraint
            $table->unique('phone'); // Re-add old unique constraint if needed
        });
    }
};
