<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            // Folder path (e.g. "clients/imgs/products")
            $table->string('folder', 500)->default('')->after('id')->index();
            // Human-readable display title
            $table->string('title')->nullable()->after('url');
            // MIME type for filtering (e.g. "image/jpeg")
            $table->string('mime_type', 100)->nullable()->after('path')->index();
            // File size in bytes
            $table->unsignedBigInteger('size')->default(0)->after('mime_type');
            // Image dimensions
            $table->unsignedInteger('width')->nullable()->after('size');
            $table->unsignedInteger('height')->nullable()->after('width');
        });
    }

    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex(['folder']);
            $table->dropIndex(['mime_type']);
            $table->dropColumn(['folder', 'title', 'mime_type', 'size', 'width', 'height']);
        });
    }
};
