<?php

namespace App\Services\Media;

use App\Models\Media;

/**
 * Scans the filesystem and syncs with the media database.
 *
 * Three operations:
 * 1. importOrphans() — Files on disk with no DB record → create DB records
 * 2. markMissing()  — DB records whose file no longer exists → mark as 'missing'
 * 3. scan()         — Run both above
 */
class MediaScanner
{
    public function __construct(
        private readonly FilesystemService $fs
    ) {}

    /**
     * Full scan: import orphans + mark missing.
     * Optionally scope to a specific folder.
     */
    public function scan(?string $folder = null): array
    {
        $report = [
            'scanned_files'     => 0,
            'imported'          => 0,
            'already_indexed'   => 0,
            'marked_missing'    => 0,
            'errors'            => [],
        ];

        $orphanReport  = $this->importOrphans($folder);
        $missingReport = $this->markMissing($folder);

        $report['scanned_files']   = $orphanReport['scanned'];
        $report['imported']        = $orphanReport['imported'];
        $report['already_indexed'] = $orphanReport['already_indexed'];
        $report['marked_missing']  = $missingReport['marked'];
        $report['errors']          = array_merge($orphanReport['errors'], $missingReport['errors']);

        return $report;
    }

    /**
     * Scan filesystem and create DB records for files not yet indexed.
     */
    public function importOrphans(?string $folder = null): array
    {
        $report = [
            'scanned'        => 0,
            'imported'       => 0,
            'already_indexed'=> 0,
            'errors'         => [],
        ];

        $files = $folder
            ? $this->fs->listFilesRecursive($this->fs->normalizePath($folder))
            : $this->fs->listFilesRecursive();

        foreach ($files as $fileInfo) {
            $report['scanned']++;

            $relPath  = $fileInfo['relative_path'];
            $filename = $fileInfo['filename'];
            $fileDir  = dirname($relPath);
            $fileDir  = $fileDir === '.' ? '' : $fileDir;

            // Check if already in DB
            $exists = Media::where('folder', $fileDir)
                ->where('filename', $filename)
                ->exists();

            if ($exists) {
                $report['already_indexed']++;
                continue;
            }

            try {
                $absPath = $this->fs->absolutePath($relPath);
                $mime    = mime_content_type($absPath) ?: null;
                $size    = filesize($absPath);
                $ext     = strtolower(pathinfo($filename, PATHINFO_EXTENSION)) ?: null;
                [$w, $h] = $this->fs->getImageDimensions($relPath);

                Media::create([
                    'folder'        => $fileDir,
                    'filename'      => $filename,
                    'original_name' => $filename,
                    'extension'     => $ext,
                    'mime_type'     => $mime,
                    'size'          => $size,
                    'width'         => $w,
                    'height'        => $h,
                    'title'         => pathinfo($filename, PATHINFO_FILENAME),
                    'status'        => 'active',
                ]);

                $report['imported']++;
            } catch (\Throwable $e) {
                $report['errors'][] = "Import failed [{$relPath}]: " . $e->getMessage();
            }
        }

        return $report;
    }

    /**
     * Find DB records whose physical file no longer exists and mark them 'missing'.
     */
    public function markMissing(?string $folder = null): array
    {
        $report = ['marked' => 0, 'errors' => []];

        $query = Media::active();
        if ($folder) {
            $normalized = $this->fs->normalizePath($folder);
            $query->where(function ($q) use ($normalized) {
                $q->where('folder', $normalized)
                  ->orWhere('folder', 'like', $normalized . '/%');
            });
        }

        $query->chunkById(500, function ($records) use (&$report) {
            foreach ($records as $media) {
                try {
                    if (!$media->existsOnDisk()) {
                        $media->update(['status' => 'missing']);
                        $report['marked']++;
                    }
                } catch (\Throwable $e) {
                    $report['errors'][] = "Mark missing failed [ID {$media->id}]: " . $e->getMessage();
                }
            }
        });

        return $report;
    }

    /**
     * Restore previously-missing records that now exist on disk again.
     */
    public function restoreFound(): int
    {
        $restored = 0;
        Media::where('status', 'missing')
            ->chunkById(500, function ($records) use (&$restored) {
                foreach ($records as $media) {
                    if ($media->existsOnDisk()) {
                        $media->update(['status' => 'active']);
                        $restored++;
                    }
                }
            });
        return $restored;
    }

    /**
     * Health report: total counts, missing, orphans (approx), broken.
     */
    public function healthReport(?string $folder = null): array
    {
        $dbQuery = Media::query();
        if ($folder) {
            $normalized = $this->fs->normalizePath($folder);
            $dbQuery->where(function ($q) use ($normalized) {
                $q->where('folder', $normalized)
                  ->orWhere('folder', 'like', $normalized . '/%');
            });
        }

        $totalDb   = (clone $dbQuery)->count();
        $active    = (clone $dbQuery)->where('status', 'active')->count();
        $missing   = (clone $dbQuery)->where('status', 'missing')->count();

        // Count filesystem files
        $fsFiles = $folder
            ? count($this->fs->listFilesRecursive($this->fs->normalizePath($folder)))
            : count($this->fs->listFilesRecursive());

        $orphans = max(0, $fsFiles - $active);

        return [
            'total_db'      => $totalDb,
            'active'        => $active,
            'missing'       => $missing,
            'filesystem'    => $fsFiles,
            'orphan_approx' => $orphans,
        ];
    }
}
