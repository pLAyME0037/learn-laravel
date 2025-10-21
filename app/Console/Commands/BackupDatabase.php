<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to a specified directory.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/');

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s%s',
            $host,
            $username,
            $password,
            $database,
            $path,
            $filename
        );

        try {
            exec($command);
            $this->info('Database backup created successfully: ' . $path . $filename);
        } catch (\Exception $e) {
            $this->error('Database backup failed: ' . $e->getMessage());
        }
    }
}
