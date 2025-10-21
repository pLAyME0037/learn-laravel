<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response; // Add this line

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect(Storage::disk('local')->files('backups'))
            ->filter(function ($file) {
                return str_ends_with($file, '.sql');
            })
            ->map(function ($file) {
                return [
                    'name' => basename($file),
                    'size' => Storage::disk('local')->size($file),
                    'last_modified' => Storage::disk('local')->lastModified($file),
                ];
            })
            ->sortByDesc('last_modified')
            ->values();

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            Artisan::call('app:backup-database');
            return redirect()->back()->with('success', 'Database backup initiated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database backup failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::disk('local')->exists($path)) {
            return Response::download(Storage::disk('local')->path($path));
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }

    public function restore($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (! file_exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        $command = sprintf(
            'mysql -h%s -u%s -p%s %s < %s',
            $host,
            $username,
            $password,
            $database,
            $path
        );

        try {
            exec($command);
            return redirect()->back()->with('success', 'Database restored successfully from ' . $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database restore failed: ' . $e->getMessage());
        }
    }

    public function destroy($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return redirect()->back()->with('success', 'Backup file deleted successfully.');
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }
}
