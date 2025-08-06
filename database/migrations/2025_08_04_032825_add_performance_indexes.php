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
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'users_email_idx');
            
            $table->index('created_at', 'users_created_at_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('name', 'categories_name_idx');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->index(['product_id', 'category_id'], 'product_categories_product_category_idx');
            
            $table->index(['category_id', 'product_id'], 'product_categories_category_product_idx');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_idx');
            $table->dropIndex('users_created_at_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_name_idx');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropIndex('product_categories_product_category_idx');
            $table->dropIndex('product_categories_category_product_idx');
        });
    }
};
