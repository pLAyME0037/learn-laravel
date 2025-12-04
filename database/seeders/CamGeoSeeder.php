<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class CamGeoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. URLs
        $baseUrl = 'https://raw.githubusercontent.com/pLAyME0037/cam-geo/refs/heads/main';
        $urls    = [
            'provinces' => "$baseUrl/CambodiaProvinceList2023.csv",
            'districts' => "$baseUrl/CambodiaDistrictList2023.csv",
            'communes'  => "$baseUrl/CambodiaCommuneList2023.csv",
            'villages'  => "$baseUrl/CambodiaVillagesList2023.csv",
        ];

        // 2. Truncate
        Schema::disableForeignKeyConstraints();
        DB::table('villages')->truncate();
        DB::table('communes')->truncate();
        DB::table('districts')->truncate();
        DB::table('provinces')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->command->info('Tables truncated. Starting download and import...');
        $now = now();

        // =========================================================
        // STEP 1: PROVINCES
        // =========================================================
        $provincesData   = $this->fetchCsv($urls['provinces']);
        $provincesInsert = [];

        foreach ($provincesData as $p) {
            $code = $p['code'] ?? $p['procode'] ?? $p['id'] ?? null;
            if (! $code) {
                continue;
            }

            $provincesInsert[] = [
                'code'       => $code,
                'name_kh'    => $p['name_kh'] ?? $p['name_km'] ?? 'N/A',
                'name_en'    => $p['name_en'] ?? $p['english'] ?? 'N/A',
                'type'       => $p['type'] ?? 'Province',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert
        DB::table('provinces')->insert($provincesInsert);
        $this->command->info('✅ Provinces imported: ' . count($provincesInsert));

        // ✅ FIX: Define the Map so Step 2 can use it
        $provinceMap = DB::table('provinces')->pluck('id', 'code');

        // =========================================================
        // STEP 2: DISTRICTS
        // =========================================================
        $districtsData   = $this->fetchCsv($urls['districts']);
        $districtsInsert = [];

        foreach ($districtsData as $d) {
            $code = $d['code'] ?? null;
            if (! $code) {
                continue;
            }

            // District Code "0102" -> Province "01"
            $provinceCode = substr($code, 0, 2);

            if (isset($provinceMap[$provinceCode])) {
                $districtsInsert[] = [
                    'province_id' => $provinceMap[$provinceCode],
                    'code'        => $code,
                    'name_kh'     => $d['name_kh'] ?? $d['name_km'] ?? 'N/A',
                    'name_en'     => $d['name_en'] ?? $d['english'] ?? 'N/A',
                    'type'        => $d['type'] ?? 'District',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        DB::table('districts')->insert($districtsInsert);
        $this->command->info('✅ Districts imported: ' . count($districtsInsert));

        // ✅ FIX: Define the Map so Step 3 can use it
        $districtMap = DB::table('districts')->pluck('id', 'code');

        // =========================================================
        // STEP 3: COMMUNES
        // =========================================================
        $communesData   = $this->fetchCsv($urls['communes']);
        $communesInsert = [];

        foreach ($communesData as $c) {
            $code = $c['code'] ?? null;
            if (! $code) {
                continue;
            }

            // Commune "010203" -> District "0102"
            $districtCode = substr($code, 0, 4);

            if (isset($districtMap[$districtCode])) {
                $communesInsert[] = [
                    'district_id' => $districtMap[$districtCode],
                    'code'        => $code,
                    'name_kh'     => $c['name_kh'] ?? $c['name_km'] ?? 'N/A',
                    'name_en'     => $c['name_en'] ?? $c['english'] ?? 'N/A',
                    'type'        => $c['type'] ?? 'Commune',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        foreach (array_chunk($communesInsert, 500) as $chunk) {
            DB::table('communes')->insert($chunk);
        }
        $this->command->info('✅ Communes imported: ' . count($communesInsert));

        // ✅ FIX: Define the Map so Step 4 can use it
        $communeMap = DB::table('communes')->pluck('id', 'code');

        // =========================================================
        // STEP 4: VILLAGES
        // =========================================================
        // Free up memory from previous steps
        unset($provincesData, $districtsData, $communesData, $provincesInsert, $districtsInsert, $communesInsert);

        $villagesData   = $this->fetchCsv($urls['villages']);
        $villagesInsert = [];

        $bar = $this->command->getOutput()->createProgressBar(count($villagesData));
        $bar->start();

        foreach ($villagesData as $v) {
            $code = $v['code'] ?? null;
            if (! $code) {
                continue;
            }

            // Village "01020301" -> Commune "010203"
            $communeCode = substr($code, 0, 6);

            if (isset($communeMap[$communeCode])) {
                $villagesInsert[] = [
                    'commune_id' => $communeMap[$communeCode],
                    'code'       => $code,
                    'name_kh'    => $v['name_kh'] ?? $v['name_km'] ?? 'N/A',
                    'name_en'    => $v['name_en'] ?? $v['english'] ?? 'N/A',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $bar->advance();
        }

        foreach (array_chunk($villagesInsert, 1000) as $chunk) {
            DB::table('villages')->insert($chunk);
        }
        $bar->finish();

        $this->command->info("\n✅ Villages imported: " . count($villagesInsert));
    }

    private function fetchCsv(string $url): array
    {
        $this->command->info("Downloading: $url");
        $response = Http::get($url);

        if ($response->failed()) {
            $this->command->error("Failed to download: $url");
            return [];
        }

        $lines     = explode("\n", trim($response->body()));
        $headerRow = array_shift($lines);
        $headerRow = preg_replace('/^\xEF\xBB\xBF/', '', $headerRow); // Remove BOM

        $headers = str_getcsv($headerRow);

        // Normalize headers
        $headers = array_map(function ($h) {
            $h = strtolower(trim($h));
            return str_replace([' ', '(', ')', '.', '/'], '_', $h);
        }, $headers);

        $data = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $row = str_getcsv($line);
            if (count($headers) === count($row)) {
                $data[] = array_combine($headers, $row);
            }
        }
        return $data;
    }
}
