<?php

namespace App\Console\Commands;

use App\Services\Media\FilesystemService;
use App\Services\Media\MediaScanner;
use Illuminate\Console\Command;

class MediaScanCommand extends Command
{
    protected $signature   = 'media:scan {folder? : Relative folder path to scan (optional)}';
    protected $description = 'Scan filesystem and sync media database (import orphans + mark missing)';

    public function handle(MediaScanner $scanner, FilesystemService $fs): int
    {
        $folder = $this->argument('folder');

        if ($folder) {
            $folder = $fs->normalizePath($folder);
            if (!$fs->isDirectory($folder)) {
                $this->error("Folder not found: {$folder}");
                return self::FAILURE;
            }
            $this->info("Scanning folder: {$folder}");
        } else {
            $this->info('Scanning all of public/storage/...');
        }

        $this->line('');

        $report = $scanner->scan($folder);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Files on filesystem', $report['scanned_files']],
                ['Newly imported', $report['imported']],
                ['Already indexed', $report['already_indexed']],
                ['Marked as missing', $report['marked_missing']],
                ['Errors', count($report['errors'])],
            ]
        );

        if ($report['errors']) {
            $this->warn('Errors:');
            foreach ($report['errors'] as $err) {
                $this->warn('  ' . $err);
            }
        }

        $this->newLine();
        $this->info('Scan complete.');

        return self::SUCCESS;
    }
}
