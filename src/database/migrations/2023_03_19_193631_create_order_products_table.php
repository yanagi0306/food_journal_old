<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            // カラム定義
            $table->id()->comment('注文商品ID');
            $table->unsignedInteger('order_id')->comment('注文ID');
            $table->string('product_name')->comment('注文商品名');
            $table->integer('quantity')->comment('注文商品数量');
            $table->integer('unit_price')->comment('商品単価');
            $table->string('order_options', 255)->nullable()->comment('注文オプション（細かな注文要望情報を記載）');
            $table->timestamps();

            // 外部キー制約の設定
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // 文字コードと照合順序の設定
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

        });
        // テーブルコメントの設定
        DB::statement("COMMENT ON TABLE order_products IS '注文商品テーブル'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
