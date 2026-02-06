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
        $driver = Schema::getConnection()->getDriverName();

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->decimal('price', 10, 2);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->boolean('in_stock')->default(false);
            $table->float('rating')->default(0);
            $table->timestamps();

            $table->index('price');
            $table->index('category_id');
            $table->index('in_stock');
            $table->index('rating');
            $table->index('created_at');
        });

        // SQLite doesn't support Laravel's schema fulltext index creation.
        if ($driver !== 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->fullText('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
