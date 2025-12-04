<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $path = database_path('dispute_db_server.sql');

        if (! File::exists($path)) {
            $this->command->error("SQL file not found.");
            return;
        }

        $this->command->info('Processing SQL file...');

        // 1. Disable Foreign Keys for SQLite
        DB::connection('sqlite_locations')->statement('PRAGMA foreign_keys = OFF;');

        // 2. Read and Clean
        $sqlContent = File::get($path);
        $sqlContent = str_replace('`', '"', $sqlContent);
        $sqlContent = str_replace("\\'", "''", $sqlContent);

        // Remove Comments
        $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

        $statements = preg_split('/;\s*[\r\n]+/', $sqlContent);
        $count      = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }

            if (stripos($statement, 'INSERT INTO') === 0) {
                try {
                    DB::connection('sqlite_locations')->statement($statement);
                    $count++;
                } catch (\Exception $e) {
                    // --- ERROR DEBUGGING ---
                    $this->command->error("❌ Failed Statement:");
                    // Print first 100 chars of query to identify table
                    $this->command->warn(substr($statement, 0, 10) . '...');
                    // Print the actual SQL Error
                    $this->command->error("Reason: " . $e->getMessage());
                    return; // Stop on first error so you can see it
                }
            }
        }

        DB::connection('sqlite_locations')->statement('PRAGMA foreign_keys = ON;');
        $this->command->info("✅ Success! Imported $count INSERT statements.");
    }
}
