<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        \JiagBrody\LaravelFacturaMx\Models\InvoiceStatus::insert([
            ['id' => InvoiceStatusEnum::DRAFT->value, 'name' => InvoiceStatusEnum::DRAFT->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceStatusEnum::VIGENT->value, 'name' => InvoiceStatusEnum::VIGENT->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceStatusEnum::CANCELED->value, 'name' => InvoiceStatusEnum::CANCELED->getName(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_statuses');
    }
};
