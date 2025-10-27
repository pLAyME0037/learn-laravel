<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize existing Sec_Ch_Ua_Platform and Sec_Ch_Ua values by stripping surrounding quotes
        DB::statement("UPDATE login_histories SET Sec_Ch_Ua_Platform = TRIM(REPLACE(Sec_Ch_Ua_Platform, '\"', '')) WHERE Sec_Ch_Ua_Platform IS NOT NULL;");
        DB::statement("UPDATE login_histories SET Sec_Ch_Ua = TRIM(REPLACE(Sec_Ch_Ua, '\"', '')) WHERE Sec_Ch_Ua IS NOT NULL;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: we won't re-add quotes
    }
};
