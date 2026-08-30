<?php

namespace App\Services\Media;

use RuntimeException;

/**
 * Abstraction over the public/storage/ filesystem.
 *
 * Storage Root = public_path('storage')
 * All paths passed to this service are RELATIVE to the storage root.
 * Example relative path: 'clients/imgs/products'
 * Example relative file: 'clients/imgs/products/abc.jpg'
 */
class FilesystemService
{
    private string $root;

    public function __construct()
    {
        $this->root = public_path('storage');
    }

    /**
     * Absolute path to storage root.
     */
    public function storageRoot(): string
    {
        return $this->root;
    }

    /**
     * Convert relative path to absolute path.
     * Validates against path traversal.
     */
    public function absolutePath(string $relativePath): string
    {
        $relative = $this->normalizePath($relativePath);
        $this->assertSafe($relative);
        return $this->root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }

    /**
     * Convert absolute path to relative path.
     */
    public function relativePath(string $absolutePath): string
    {
        $abs = realpath($absolutePath) ?: $absolutePath;
        $base = realpath($this->root) ?: $this->root;

        if (!str_starts_with($abs, $base)) {
            throw new RuntimeException("Path is outside storage root: {$absolutePath}");
        }

        return ltrim(str_replace(DIRECTORY_SEPARATOR, '/', substr($abs, strlen($base))), '/');
    }

    /**
     * Normalize a path: remove double slashes, trim slashes, convert backslashes.
     */
    public function normalizePath(string $path): string
    {
        $path = str_replace(['\\', '//'], ['/', '/'], $path);
        $path = trim($path, '/');

        // Resolve . and .. segments
        $parts   = explode('/', $path);
        $result  = [];
        foreach ($parts as $part) {
            if ($part === '' || $part === '.') continue;
            if ($part === '..') {
                array_pop($result);
            } else {
                $result[] = $part;
            }
        }
        return implode('/', $result);
    }

    /**
     * Assert path does not escape storage root.
     */
    public function assertSafe(string $normalizedRelative): void
    {
        if (str_contains($normalizedRelative, '..')) {
            throw new RuntimeException("Path traversal detected: {$normalizedRelative}");
        }
    }

    /**
     * Validate that a filename is safe (no slashes, no null bytes, no traversal).
     */
    public function validateFilename(string $filename): void
    {
        if (
            str_contains($filename, '/') ||
            str_contains($filename, '\\') ||
            str_contains($filename, "\0") ||
            str_contains($filename, '..')
        ) {
            throw new RuntimeException("Invalid filename: {$filename}");
        }
    }

    /**
     * Check if a relative path (file or directory) exists.
     */
    public function exists(string $relativePath): bool
    {
        return file_exists($this->absolutePath($relativePath));
    }

    /**
     * Check if path is a directory.
     */
    public function isDirectory(string $relativePath): bool
    {
        return is_dir($this->absolutePath($relativePath));
    }

    /**
     * List immediate subdirectories of a given relative path.
     * Returns array of relative paths.
     */
    public function listDirectories(string $relativePath = ''): array
    {
        $abs = $relativePath === '' ? $this->root : $this->absolutePath($relativePath);

        if (!is_dir($abs)) {
            return [];
        }

        $dirs = [];
        foreach (scandir($abs) as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $full = $abs . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full)) {
                $rel   = $relativePath === '' ? $entry : $relativePath . '/' . $entry;
                $dirs[] = $rel;
            }
        }
        return $dirs;
    }

    /**
     * List files in a given relative path (non-recursive).
     * Returns array of ['filename', 'relative_path', 'size', 'mtime'].
     */
    public function listFiles(string $relativePath = ''): array
    {
        $abs = $relativePath === '' ? $this->root : $this->absolutePath($relativePath);

        if (!is_dir($abs)) {
            return [];
        }

        $files = [];
        foreach (scandir($abs) as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $full = $abs . DIRECTORY_SEPARATOR . $entry;
            if (is_file($full)) {
                $files[] = [
                    'filename'      => $entry,
                    'relative_path' => ($relativePath ? $relativePath . '/' : '') . $entry,
                    'size'          => filesize($full),
                    'mtime'         => filemtime($full),
                ];
            }
        }
        return $files;
    }

    /**
     * Recursively list all files under a given relative path.
     */
    public function listFilesRecursive(string $relativePath = ''): array
    {
        $files = $this->listFiles($relativePath);
        foreach ($this->listDirectories($relativePath) as $subDir) {
            $files = array_merge($files, $this->listFilesRecursive($subDir));
        }
        return $files;
    }

    /**
     * Build a nested folder tree from a given root relative path.
     * Returns nested array: [name, path, children[]]
     */
    public function buildFolderTree(string $relativePath = ''): array
    {
        $dirs = $this->listDirectories($relativePath);
        $tree = [];
        foreach ($dirs as $dir) {
            $name     = basename($dir);
            $children = $this->buildFolderTree($dir);
            $tree[]   = [
                'name'     => $name,
                'path'     => $dir,
                'children' => $children,
            ];
        }
        return $tree;
    }

    /**
     * Create a directory (recursive).
     */
    public function makeDirectory(string $relativePath): bool
    {
        $abs = $this->absolutePath($relativePath);
        if (is_dir($abs)) {
            return true;
        }
        return mkdir($abs, 0755, true);
    }

    /**
     * Rename a file or directory.
     * Both paths are relative to storage root.
     */
    public function rename(string $oldRelative, string $newRelative): bool
    {
        $old = $this->absolutePath($oldRelative);
        $new = $this->absolutePath($newRelative);

        if (!file_exists($old)) {
            throw new RuntimeException("Source not found: {$oldRelative}");
        }
        if (file_exists($new)) {
            throw new RuntimeException("Destination already exists: {$newRelative}");
        }

        return rename($old, $new);
    }

    /**
     * Move a file to a different folder.
     * srcRelativeFile: 'clients/imgs/products/abc.jpg'
     * destFolder: 'clients/imgs/posts'
     * Returns new relative path.
     */
    public function moveFile(string $srcRelativeFile, string $destFolder): string
    {
        $this->assertSafe($this->normalizePath($srcRelativeFile));
        $this->assertSafe($this->normalizePath($destFolder));

        $filename    = basename($srcRelativeFile);
        $srcAbs      = $this->absolutePath($srcRelativeFile);
        $destDirAbs  = $this->absolutePath($destFolder);
        $destFileAbs = $destDirAbs . DIRECTORY_SEPARATOR . $filename;
        $destRelative = rtrim($destFolder, '/') . '/' . $filename;

        if (!file_exists($srcAbs)) {
            throw new RuntimeException("Source file not found: {$srcRelativeFile}");
        }
        if (!is_dir($destDirAbs)) {
            throw new RuntimeException("Destination folder not found: {$destFolder}");
        }
        if (file_exists($destFileAbs)) {
            throw new RuntimeException("A file with the same name already exists in destination: {$filename}");
        }

        if (!rename($srcAbs, $destFileAbs)) {
            throw new RuntimeException("Failed to move file: {$srcRelativeFile} → {$destRelative}");
        }

        return $destRelative;
    }

    /**
     * Delete a single file.
     */
    public function deleteFile(string $relativeFile): bool
    {
        $abs = $this->absolutePath($relativeFile);
        if (!file_exists($abs)) {
            return true; // Already gone — treat as success
        }
        return unlink($abs);
    }

    /**
     * Delete a directory and all its contents.
     */
    public function deleteDirectory(string $relativePath): bool
    {
        $abs = $this->absolutePath($relativePath);
        if (!is_dir($abs)) {
            return true;
        }
        return $this->deleteDirectoryRecursive($abs);
    }

    private function deleteDirectoryRecursive(string $abs): bool
    {
        foreach (scandir($abs) as $item) {
            if ($item === '.' || $item === '..') continue;
            $full = $abs . DIRECTORY_SEPARATOR . $item;
            if (is_dir($full)) {
                $this->deleteDirectoryRecursive($full);
            } else {
                unlink($full);
            }
        }
        return rmdir($abs);
    }

    /**
     * Get MIME type of a file from its content (not extension).
     */
    public function getMimeType(string $relativeFile): ?string
    {
        $abs = $this->absolutePath($relativeFile);
        if (!file_exists($abs)) {
            return null;
        }
        $mime = mime_content_type($abs);
        return $mime ?: null;
    }

    /**
     * Get image dimensions [width, height] or [null, null] for non-images.
     */
    public function getImageDimensions(string $relativeFile): array
    {
        $abs = $this->absolutePath($relativeFile);
        if (!file_exists($abs)) {
            return [null, null];
        }
        try {
            $info = @getimagesize($abs);
            return $info ? [(int)$info[0] ?: null, (int)$info[1] ?: null] : [null, null];
        } catch (\Throwable) {
            return [null, null];
        }
    }

    /**
     * Compute MD5 checksum of a file.
     */
    public function checksum(string $relativeFile): ?string
    {
        $abs = $this->absolutePath($relativeFile);
        return file_exists($abs) ? md5_file($abs) : null;
    }
}
