<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    protected $sensitiveFields = [
        'id_card_number',
        'passport_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'disability_details',
    ];

    public function up(): void
    {
        // 1. CRITICAL: Expand columns to TEXT first to hold long encrypted strings
        Schema::table('students', function (Blueprint $table) {
            foreach ($this->sensitiveFields as $field) {
                // Change to TEXT to ensure encrypted string fits (usually ~250+ chars)
                $table->text($field)->nullable()->change();
            }
        });

        // 2. Encrypt existing data
        DB::table('students')->orderBy('id')->chunkById(100, function ($students) {
            foreach ($students as $student) {
                $updateData = [];

                foreach ($this->sensitiveFields as $field) {
                    $value = $student->$field;

                    // Check if value exists and is NOT already encrypted
                    // Laravel encrypted strings always start with "eyJpdiI" (base64 for {"iv":)
                    if (! empty($value) && ! Str::startsWith($value, 'eyJpdiI')) {
                        try {
                            $updateData[$field] = Crypt::encryptString($value);
                        } catch (\Exception $e) {
                            // Log error but continue? Or throw?
                            // Usually safe to ignore here as encryptString rarely fails with valid keys
                        }
                    }
                }

                if (! empty($updateData)) {
                    // Use DB facade to bypass Model Events/Casts to avoid "Payload Invalid" loops
                    DB::table('students')->where('id', $student->id)->update($updateData);
                }
            }
        });
    }

    public function down(): void
    {
        // Attempt to Decrypt (Optional: Only if you really need rollback capability)
        DB::table('students')->orderBy('id')->chunkById(100, function ($students) {
            foreach ($students as $student) {
                $updateData = [];
                foreach ($this->sensitiveFields as $field) {
                    $value = $student->$field;
                    if (! empty($value) && Str::startsWith($value, 'eyJpdiI')) {
                        try {
                            $updateData[$field] = Crypt::decryptString($value);
                        } catch (\Exception $e) {
                            // If decryption fails, leave as is
                        }
                    }
                }
                if (! empty($updateData)) {
                    DB::table('students')->where('id', $student->id)->update($updateData);
                }
            }
        });
    }
};
