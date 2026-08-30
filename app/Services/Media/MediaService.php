<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class MediaService
{
    public function __construct(
        private readonly FilesystemService $fs
    ) {}

    // ─────────────────── UPLOAD ───────────────────

    /**
     * Upload a single file to a folder.
     * Flow: validate → save filesystem → verify → read metadata → create DB → return.
     * DB is NEVER written before filesystem save succeeds.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder  Relative to storage root, e.g. 'clients/imgs/products'
     * @param  int           $maxBytes  Max allowed size in bytes (default 5MB)
     * @return Media
     */
    public function upload(UploadedFile $file, string $folder, int $maxBytes = 5 * 1024 * 1024): Media
    {
        // 1. Validate folder path (no traversal)
        $folder = $this->fs->normalizePath($folder);
        $this->fs->assertSafe($folder);

        // 2. Validate file
        if (!$file->isValid()) {
            throw new RuntimeException('File không hợp lệ: ' . $file->getErrorMessage());
        }

        if ($file->getSize() > $maxBytes) {
            throw new RuntimeException(sprintf(
                'File "%s" vượt quá giới hạn %.1fMB.',
                $file->getClientOriginalName(),
                $maxBytes / 1_048_576
            ));
        }

        // 3. Ensure destination folder exists
        if (!$this->fs->isDirectory($folder)) {
            $this->fs->makeDirectory($folder);
        }

        // 4. Build safe filename
        $originalName = $file->getClientOriginalName();
        $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $slug         = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: Str::random(8);
        $filename     = $slug . ($ext ? '.' . $ext : '');

        // 5. Resolve collisions
        $filename = $this->resolveCollision($folder, $filename);

        // 6. Save file to filesystem
        $relativeFile = $folder . '/' . $filename;
        $absPath      = $this->fs->absolutePath($folder) . DIRECTORY_SEPARATOR . $filename;

        $moved = $file->move(dirname($absPath), $filename);
        if (!$moved || !file_exists($absPath)) {
            throw new RuntimeException('Lưu file thất bại: ' . $relativeFile);
        }

        // 7. Read metadata from saved file (not from client upload!)
        $mime          = mime_content_type($absPath) ?: $file->getMimeType();
        $size          = filesize($absPath);
        [$width, $height] = $this->fs->getImageDimensions($relativeFile);

        // 8. Create DB record AFTER filesystem success
        $media = Media::create([
            'folder'        => $folder,
            'filename'      => $filename,
            'original_name' => $originalName,
            'extension'     => $ext ?: null,
            'mime_type'     => $mime,
            'size'          => $size,
            'width'         => $width,
            'height'        => $height,
            'title'         => pathinfo($originalName, PATHINFO_FILENAME),
            'status'        => 'active',
        ]);

        return $media;
    }

    // ─────────────────── RENAME ───────────────────

    /**
     * Rename a file.
     * Flow: filesystem rename → verify → DB update.
     * DB is NEVER updated if filesystem rename fails.
     */
    public function rename(int $mediaId, string $newFilename): Media
    {
        $media = Media::findOrFail($mediaId);

        // Validate new filename
        $this->fs->validateFilename($newFilename);

        // Preserve extension if not provided
        $oldExt = strtolower(pathinfo($media->filename, PATHINFO_EXTENSION));
        $newExt = strtolower(pathinfo($newFilename, PATHINFO_EXTENSION));
        if (!$newExt && $oldExt) {
            $newFilename .= '.' . $oldExt;
        }

        $slug        = Str::slug(pathinfo($newFilename, PATHINFO_FILENAME)) ?: Str::random(8);
        $newFilename = $slug . ($newExt ? '.' . $newExt : ($oldExt ? '.' . $oldExt : ''));

        if ($newFilename === $media->filename) {
            return $media; // Nothing to do
        }

        // Check collision
        $newFilename = $this->resolveCollision($media->folder, $newFilename, $media->id);

        $oldRelative = $media->relative_path;
        $newRelative = ($media->folder ? $media->folder . '/' : '') . $newFilename;

        // Filesystem first
        if (!$this->fs->rename($oldRelative, $newRelative)) {
            throw new RuntimeException('Đổi tên file thất bại trên filesystem.');
        }

        // Verify
        if (!$this->fs->exists($newRelative)) {
            throw new RuntimeException('Đổi tên thất bại: file mới không tồn tại.');
        }

        // DB update only after filesystem success
        $media->update([
            'filename'  => $newFilename,
            'extension' => strtolower(pathinfo($newFilename, PATHINFO_EXTENSION)) ?: null,
        ]);

        return $media->fresh();
    }

    // ─────────────────── MOVE ───────────────────

    /**
     * Move a file to a different folder.
     * Flow: filesystem move → verify → DB update.
     */
    public function move(int $mediaId, string $destFolder): Media
    {
        $media = Media::findOrFail($mediaId);

        $destFolder = $this->fs->normalizePath($destFolder);
        $this->fs->assertSafe($destFolder);

        if ($destFolder === $media->folder) {
            return $media; // Nothing to do
        }

        if (!$this->fs->isDirectory($destFolder)) {
            throw new RuntimeException("Thư mục đích không tồn tại: {$destFolder}");
        }

        // Filesystem move first
        $newRelative = $this->fs->moveFile($media->relative_path, $destFolder);

        // Verify
        if (!$this->fs->exists($newRelative)) {
            throw new RuntimeException('Di chuyển file thất bại: file không xuất hiện ở đích.');
        }

        // DB update
        $media->update(['folder' => $destFolder]);

        return $media->fresh();
    }

    // ─────────────────── DELETE ───────────────────

    /**
     * Delete a single media file.
     * Flow: filesystem delete → verify gone → DB delete.
     */
    public function delete(int $mediaId): bool
    {
        $media = Media::findOrFail($mediaId);

        $relativeFile = $media->relative_path;

        // Filesystem first
        if ($this->fs->exists($relativeFile)) {
            if (!$this->fs->deleteFile($relativeFile)) {
                throw new RuntimeException('Xóa file thất bại trên filesystem: ' . $relativeFile);
            }
        }

        // DB delete
        $media->delete();

        return true;
    }

    /**
     * Bulk delete multiple media files.
     */
    public function bulkDelete(array $ids): array
    {
        $results = ['deleted' => 0, 'errors' => []];

        $items = Media::whereIn('id', $ids)->get();
        foreach ($items as $media) {
            try {
                $this->delete($media->id);
                $results['deleted']++;
            } catch (\Throwable $e) {
                $results['errors'][] = "ID {$media->id}: " . $e->getMessage();
            }
        }

        return $results;
    }

    // ─────────────────── FOLDER MANAGEMENT ───────────────────

    /**
     * Create a new folder.
     */
    public function createFolder(string $relativePath): bool
    {
        $relative = $this->fs->normalizePath($relativePath);
        $this->fs->assertSafe($relative);

        if ($this->fs->isDirectory($relative)) {
            throw new RuntimeException("Thư mục đã tồn tại: {$relative}");
        }

        return $this->fs->makeDirectory($relative);
    }

    /**
     * Rename a folder and update all media records inside it.
     * Flow: filesystem rename → verify → DB update (batch).
     */
    public function renameFolder(string $oldRelative, string $newRelative): int
    {
        $oldRelative = $this->fs->normalizePath($oldRelative);
        $newRelative = $this->fs->normalizePath($newRelative);

        if (!$this->fs->isDirectory($oldRelative)) {
            throw new RuntimeException("Thư mục nguồn không tồn tại: {$oldRelative}");
        }
        if ($this->fs->exists($newRelative)) {
            throw new RuntimeException("Thư mục đích đã tồn tại: {$newRelative}");
        }

        // Filesystem first
        if (!$this->fs->rename($oldRelative, $newRelative)) {
            throw new RuntimeException('Đổi tên thư mục thất bại trên filesystem.');
        }

        // Verify
        if (!$this->fs->isDirectory($newRelative)) {
            throw new RuntimeException('Đổi tên thư mục thất bại: thư mục mới không tồn tại.');
        }

        // DB: update all media records whose folder starts with oldRelative
        $affected = 0;
        $records = Media::where('folder', $oldRelative)
            ->orWhere('folder', 'like', $oldRelative . '/%')
            ->get();

        foreach ($records as $record) {
            $newFolder = $newRelative . substr($record->folder, strlen($oldRelative));
            $record->update(['folder' => $newFolder]);
            $affected++;
        }

        return $affected;
    }

    /**
     * Delete a folder and all its contents (filesystem + DB).
     */
    public function deleteFolder(string $relativePath, bool $force = false): array
    {
        $relative = $this->fs->normalizePath($relativePath);
        $this->fs->assertSafe($relative);

        if (!$this->fs->isDirectory($relative)) {
            throw new RuntimeException("Thư mục không tồn tại: {$relative}");
        }

        $filesInFolder = $this->fs->listFilesRecursive($relative);
        if (!$force && count($filesInFolder) > 0) {
            throw new RuntimeException(
                "Thư mục không rỗng ({$count_files} file). Dùng force=true để xóa tất cả."
            );
        }

        // Delete filesystem
        $this->fs->deleteDirectory($relative);

        // DB: remove all media records in this folder
        $deleted = Media::where('folder', $relative)
            ->orWhere('folder', 'like', $relative . '/%')
            ->delete();

        return ['folders_removed' => 1, 'db_records_deleted' => $deleted];
    }

    // ─────────────────── FOLDER TREE ───────────────────

    /**
     * Build folder tree from real filesystem.
     */
    public function folderTree(): array
    {
        return $this->fs->buildFolderTree();
    }

    // ─────────────────── BROWSE ───────────────────

    /**
     * Browse a folder: return files from DB + subfolders from filesystem.
     */
    public function browse(
        string $folder,
        string $search = '',
        string $sort = 'newest',
        string $type = 'all',
        int $page = 1,
        int $perPage = 48
    ): array {
        $folder = $this->fs->normalizePath($folder);

        // Files from DB
        $query = Media::active()->inFolder($folder);

        if ($search) {
            $query->search($search);
        }

        match ($type) {
            'image'    => $query->images(),
            'video'    => $query->where('mime_type', 'like', 'video/%'),
            'document' => $query->whereIn('mime_type', [
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
            ]),
            default    => null,
        };

        match ($sort) {
            'oldest'  => $query->orderBy('created_at'),
            'name_az' => $query->orderBy('filename'),
            'name_za' => $query->orderByDesc('filename'),
            'largest' => $query->orderByDesc('size'),
            'smallest'=> $query->orderBy('size'),
            default   => $query->orderByDesc('created_at'),
        };

        $total   = $query->count();
        $records = $query->forPage($page, $perPage)->get();

        // Subfolders from filesystem
        $subfolders = collect($this->fs->listDirectories($folder))
            ->map(fn($path) => [
                'type'  => 'folder',
                'name'  => basename($path),
                'path'  => $path,
                'count' => Media::active()->where('folder', $path)->count(),
            ])
            ->values();

        return [
            'folder'     => $folder,
            'subfolders' => $subfolders,
            'files'      => $records->map->toApiArray()->values(),
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'has_more'   => ($page * $perPage) < $total,
        ];
    }

    // ─────────────────── METADATA UPDATE ───────────────────

    /**
     * Update editorial metadata (title, alt, notes).
     */
    public function updateMetadata(int $mediaId, array $data): Media
    {
        $media = Media::findOrFail($mediaId);
        $media->update(array_intersect_key($data, array_flip(['title', 'alt', 'notes'])));
        return $media->fresh();
    }

    // ─────────────────── HELPERS ───────────────────

    /**
     * Resolve filename collision by appending incrementing suffix.
     * If excludeId is given, that record is excluded from uniqueness check (for renames).
     */
    private function resolveCollision(string $folder, string $filename, ?int $excludeId = null): string
    {
        $absDir = $this->fs->absolutePath($folder);
        $ext    = pathinfo($filename, PATHINFO_EXTENSION);
        $base   = pathinfo($filename, PATHINFO_FILENAME);

        $candidate = $filename;
        $i = 1;

        while (file_exists($absDir . DIRECTORY_SEPARATOR . $candidate)) {
            if ($excludeId && $candidate === $filename) {
                // If renaming to same name, it's fine (it's the same file)
                $query = Media::where('folder', $folder)->where('filename', $candidate);
                if ($excludeId) $query->where('id', '!=', $excludeId);
                if (!$query->exists()) break;
            }
            $candidate = $base . '-' . $i . ($ext ? '.' . $ext : '');
            $i++;
        }

        return $candidate;
    }
}
