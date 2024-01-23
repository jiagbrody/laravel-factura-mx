<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_cfdi_cancel_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        \JiagBrody\LaravelFacturaMx\Models\InvoiceCfdiCancelType::insert([
            ['id' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_UNRELATED->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_UNRELATED->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceCfdiCancelTypeEnum::NEW_NORMATIVE_TO_GLOBAL->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_NORMATIVE_TO_GLOBAL->getName(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_cfdi_cancel_types');
    }
};
