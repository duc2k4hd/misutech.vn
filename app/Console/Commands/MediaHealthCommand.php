<?php

namespace App\Console\Commands;

use App\Services\Media\FilesystemService;
use App\Services\Media\MediaScanner;
use Illuminate\Console\Command;

class MediaHealthCommand extends Command
{
    protected $signature   = 'media:health {folder? : Optional folder to scope the check}';
    protected $description = 'Show filesystem vs database health report for media';

    public function handle(MediaScanner $scanner, FilesystemService $fs): int
    {
        $folder = $this->argument('folder');

        if ($folder) {
            $folder = $fs->normalizePath($folder);
        }

        $report = $scanner->healthReport($folder ?: null);

        $this->newLine();
        $this->line('<fg=cyan;options=bold>═══ MEDIA HEALTH REPORT ═══</>');
        $this->newLine();

        $this->table(
            ['Category', 'Count'],
            [
                ['📁 Total DB records',       $report['total_db']],
                ['✅ Active (file exists)',   $report['active']],
                ['❌ Missing (file deleted)', $report['missing']],
                ['📂 Files on filesystem',    $report['filesystem']],
                ['🔍 Unindexed (approx)',     $report['orphan_approx']],
            ]
        );

        if ($report['missing'] > 0) {
            $this->warn("⚠  {$report['missing']} DB record(s) have no matching file. Run: php artisan media:scan");
        }
        if ($report['orphan_approx'] > 0) {
            $this->warn("⚠  ~{$report['orphan_approx']} file(s) on disk not indexed. Run: php artisan media:scan");
        }
        if ($report['missing'] === 0 && $report['orphan_approx'] === 0) {
            $this->info('✅ All good! Filesystem and database are in sync.');
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
