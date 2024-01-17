<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->string('extension');
            $table->string('storage')->comment("localización del disco donde se guarda");
            $table->morphs('documentable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_documents');
    }
};
