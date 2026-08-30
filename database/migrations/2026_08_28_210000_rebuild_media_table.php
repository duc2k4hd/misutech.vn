<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old images table (0 records, safe to drop)
        Schema::dropIfExists('images');

        // Create the new media table
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            // Storage identifier (future-proof for multi-disk)
            $table->string('disk', 30)->default('public_storage')->index();

            // Path components
            // folder: relative to storage root, e.g. 'clients/imgs/products'
            $table->string('folder', 500)->default('')->index();
            // filename: just the file name, e.g. 'abc.jpg'
            $table->string('filename', 255)->index();
            // original name from client
            $table->string('original_name', 255)->nullable();
            // extension without dot, lowercase
            $table->string('extension', 20)->nullable()->index();

            // File metadata (read from filesystem, not from client)
            $table->string('mime_type', 100)->nullable()->index();
            $table->unsignedBigInteger('size')->default(0)->index();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->char('checksum', 32)->nullable()->index();

            // Editorial metadata (managed by users)
            $table->string('title', 255)->nullable();
            $table->string('alt', 255)->nullable();
            $table->text('notes')->nullable();

            // Health status: 'active' = file exists, 'missing' = file deleted from FS
            $table->enum('status', ['active', 'missing'])->default('active')->index();

            $table->timestamps();

            // Enforce uniqueness: same folder cannot have 2 files with same name
            $table->unique(['folder', 'filename'], 'uq_media_folder_filename');

            // Composite index for folder browsing queries
            $table->index(['folder', 'status', 'created_at'], 'idx_media_folder_status_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');

        // Restore old images table (minimal schema)
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('imageable');
            $table->string('url');
            $table->string('path');
            $table->string('folder', 500)->default('')->index();
            $table->string('title')->nullable();
            $table->string('mime_type', 100)->nullable()->index();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt')->nullable();
            $table->text('notes')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }
};
