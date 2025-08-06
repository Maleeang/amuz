<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::statement('
            INSERT INTO product_categories (product_id, category_id, created_at, updated_at)
            SELECT id, category_id, created_at, updated_at 
            FROM products 
            WHERE category_id IS NOT NULL
        ');
        

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
        });
        

        DB::statement('
            UPDATE products 
            SET category_id = (
                SELECT category_id 
                FROM product_categories 
                WHERE product_categories.product_id = products.id 
                LIMIT 1
            )
        ');
    }
};
