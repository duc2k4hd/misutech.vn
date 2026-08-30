<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\Media\FilesystemService;
use App\Services\Media\MediaScanner;
use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService    $mediaService,
        private readonly FilesystemService $fs,
        private readonly MediaScanner   $scanner,
    ) {}

    // ════════════════════════════════════════════════════
    //  PAGE
    // ════════════════════════════════════════════════════

    public function index(): View
    {
        return view('admins.pages.media.index');
    }

    // ════════════════════════════════════════════════════
    //  FOLDER TREE
    // ════════════════════════════════════════════════════

    /**
     * GET api/media/tree
     * Returns folder tree built from real filesystem.
     */
    public function apiFolderTree(): JsonResponse
    {
        try {
            $tree = $this->mediaService->folderTree();
            return response()->json(['success' => true, 'tree' => $tree]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  BROWSE FOLDER
    // ════════════════════════════════════════════════════

    /**
     * GET api/media?folder=...&search=...&sort=...&type=...&page=...
     * Browse a folder: returns subfolders + paginated files.
     */
    public function apiBrowse(Request $request): JsonResponse
    {
        try {
            $folder  = (string) $request->input('folder', '');
            $search  = trim((string) $request->input('search', ''));
            $sort    = (string) $request->input('sort', 'newest');
            $type    = (string) $request->input('type', 'all');
            $page    = max(1, (int) $request->input('page', 1));
            $perPage = min(200, max(12, (int) $request->input('per_page', 48)));

            $data = $this->mediaService->browse($folder, $search, $sort, $type, $page, $perPage);

            return response()->json(['success' => true] + $data);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD
    // ════════════════════════════════════════════════════

    /**
     * POST api/media/upload
     * Body: target_folder (string), files[] (UploadedFile[])
     */
    public function apiUpload(Request $request): JsonResponse
    {
        try {
            $folder = $this->fs->normalizePath((string) $request->input('target_folder', ''));
            $this->fs->assertSafe($folder);

            $files = $request->file('files');
            if (empty($files)) {
                return $this->error('Không có file nào được gửi lên.', 422);
            }
            if (!is_array($files)) {
                $files = [$files];
            }

            $uploaded = [];
            $errors   = [];

            $maxSize = 5 * 1024 * 1024; // 5MB default
            if (str_contains($folder, 'catalogs')) {
                $maxSize = 100 * 1024 * 1024; // 100MB for catalogs
            }

            foreach ($files as $file) {
                try {
                    $media      = $this->mediaService->upload($file, $folder, $maxSize);
                    $uploaded[] = $media->toApiArray();
                } catch (\Throwable $e) {
                    $errors[] = $e->getMessage();
                }
            }

            return response()->json([
                'success'  => count($uploaded) > 0,
                'message'  => 'Tải lên ' . count($uploaded) . ' file thành công!' . (count($errors) ? ' ' . count($errors) . ' lỗi.' : ''),
                'uploaded' => $uploaded,
                'errors'   => $errors,
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  UPDATE METADATA
    // ════════════════════════════════════════════════════

    /**
     * PUT api/media/{id}
     * Body: title, alt, notes
     */
    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        try {
            $media = $this->mediaService->updateMetadata($id, $request->only(['title', 'alt', 'notes']));
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công.',
                'item'    => $media->toApiArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  RENAME FILE
    // ════════════════════════════════════════════════════

    /**
     * POST api/media/{id}/rename
     * Body: filename (new name)
     */
    public function apiRename(Request $request, int $id): JsonResponse
    {
        $request->validate(['filename' => 'required|string|max:255']);

        try {
            $media = $this->mediaService->rename($id, $request->input('filename'));
            return response()->json([
                'success' => true,
                'message' => 'Đổi tên thành công.',
                'item'    => $media->toApiArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  MOVE FILE
    // ════════════════════════════════════════════════════

    /**
     * POST api/media/{id}/move
     * Body: folder (destination relative path)
     */
    public function apiMove(Request $request, int $id): JsonResponse
    {
        $request->validate(['folder' => 'required|string|max:500']);

        try {
            $media = $this->mediaService->move($id, $request->input('folder'));
            return response()->json([
                'success' => true,
                'message' => 'Di chuyển thành công.',
                'item'    => $media->toApiArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  DELETE FILE(S)
    // ════════════════════════════════════════════════════

    /**
     * POST api/media/delete
     * Body: ids[] (array of media IDs)
     */
    public function apiDelete(Request $request): JsonResponse
    {
        try {
            $ids = array_filter((array) $request->input('ids', []));
            if (empty($ids)) {
                return $this->error('Chưa chọn file nào để xóa.', 422);
            }

            $result = $this->mediaService->bulkDelete($ids);

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$result['deleted']} file." . (count($result['errors']) ? ' ' . count($result['errors']) . ' lỗi.' : ''),
                'result'  => $result,
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  FOLDER MANAGEMENT
    // ════════════════════════════════════════════════════

    /**
     * POST api/media/folders
     * Body: path (relative path for new folder)
     */
    public function apiFolderCreate(Request $request): JsonResponse
    {
        $request->validate(['path' => 'required|string|max:500']);

        try {
            $this->mediaService->createFolder($request->input('path'));
            return response()->json(['success' => true, 'message' => 'Tạo thư mục thành công.']);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * PUT api/media/folders
     * Body: old_path, new_path
     */
    public function apiFolderRename(Request $request): JsonResponse
    {
        $request->validate([
            'old_path' => 'required|string|max:500',
            'new_path' => 'required|string|max:500',
        ]);

        try {
            $affected = $this->mediaService->renameFolder(
                $request->input('old_path'),
                $request->input('new_path')
            );
            return response()->json([
                'success'  => true,
                'message'  => "Đổi tên thư mục thành công. {$affected} media records được cập nhật.",
                'affected' => $affected,
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * DELETE api/media/folders
     * Body: path, force (bool)
     */
    public function apiFolderDelete(Request $request): JsonResponse
    {
        $request->validate(['path' => 'required|string|max:500']);

        try {
            $result = $this->mediaService->deleteFolder(
                $request->input('path'),
                (bool) $request->input('force', false)
            );
            return response()->json([
                'success' => true,
                'message' => 'Xóa thư mục thành công.',
                'result'  => $result,
            ]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  SYNC
    // ════════════════════════════════════════════════════

    /**
     * POST api/media/sync
     * Body: folder (optional)
     */
    public function apiSync(Request $request): JsonResponse
    {
        try {
            $folder = $request->input('folder');
            $report = $this->scanner->scan($folder ?: null);
            return response()->json(['success' => true, 'message' => 'Sync hoàn tất.', 'report' => $report]);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // ════════════════════════════════════════════════════
    //  HELPERS
    // ════════════════════════════════════════════════════

    private function error(string $message, int $status = 500): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
