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
        Schema::create('users', function (Blueprint $table) {

            // カラム定義
            $table->id()->comment('ユーザーID');
            $table->unsignedInteger('company_id')->comment('会社ID');
            $table->unsignedInteger('store_id')->comment('店舗ID');
            $table->string('name', 30)->comment('ユーザー名');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 30)->comment('パスワード');
            $table->integer('permission')->nullable()->comment('権限（null:ロックアカウント、1:一般、2:店舗管理者、3:会社管理者）');
            $table->rememberToken();
            $table->timestamps();

            // 外部キー制約の設定
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            // 文字コードと照合順序の設定
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';

        });
        // テーブルコメントの設定
        DB::statement("COMMENT ON TABLE users IS 'ユーザーテーブル'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
