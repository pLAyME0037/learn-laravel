<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(600);

        $path = database_path('cam_geos.json');

        if (! File::exists($path)) {
            $this->command->error("File not found: $path");
            return;
        }

        $this->command->info('Reading JSON file...');
        $json = json_decode(File::get($path), true);

        if (! $json) {
            $this->command->error("Invalid JSON format.");
            return;
        }

        $connection = DB::connection('sqlite_locations');

        // 1. Disable Foreign Keys (Critical for SQLite/MySQL bulk imports)
        $connection->statement('PRAGMA foreign_keys = OFF;');
        // If using MySQL: $connection->statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Configuration: Map JSON names to DB names AND unique keys
        $tableConfig = [
            'province' => ['table' => 'provinces', 'unique' => 'prov_id'],
            'district' => ['table' => 'districts', 'unique' => 'dist_id'],
            'commune'  => ['table' => 'communes', 'unique' => 'comm_id'],
            'village'  => ['table' => 'villages', 'unique' => 'vill_id'],
        ];

        foreach ($json as $block) {
            if (! isset($block['type']) || $block['type'] !== 'table') {
                continue;
            }

            $jsonName = $block['name'];

            if (! isset($tableConfig[$jsonName])) {
                continue;
            }

            $config    = $tableConfig[$jsonName];
            $dbTable   = $config['table'];
            $uniqueKey = $config['unique'];

            $rows  = $block['data'];
            $count = count($rows);

            $this->command->info("Processing $dbTable ($count rows)...");

            // 3. Process in chunks
            $chunks = array_chunk($rows, 500);

            foreach ($chunks as $chunk) {
                // Clean up the data (handle empty strings for nullable fields)
                $cleanChunk = array_map(function ($row) {
                    // Convert "is_not_active": "" to null if present
                    if (array_key_exists('is_not_active', $row) && $row['is_not_active'] === "") {
                        $row['is_not_active'] = null;
                    }
                    return $row;
                }, $chunk);

                // 4. Use Upsert instead of Insert
                // This prevents "UNIQUE constraint failed" errors
                $connection->table($dbTable)->upsert(
                    $cleanChunk,
                    [$uniqueKey], // The column that must be unique
                                  // Columns to update if duplicate found (update everything except ID/Unique)
                    array_diff(array_keys($cleanChunk[0]), ['id', $uniqueKey])
                );
            }
        }

        // 5. Re-enable Foreign Keys
        $connection->statement('PRAGMA foreign_keys = ON;');
        // If using MySQL: $connection->statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Location seeding completed successfully!');
    }
}
