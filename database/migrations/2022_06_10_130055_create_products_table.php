<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sku');
            $table->integer('inventory')->default(1);
            $table->string('manufacture');
            $table->text('image')->nullable();
            $table->text('thumbnail')->nullable();
            $table->json('name');
            $table->json('description');
            $table->json('price');
            $table->json('currency', 3);
            $table->boolean('active')->default(true);
            $table->boolean('is_vat_included')->default(true);
            $table->foreignId('user_id')->constrained('users');
            $table->unique(['sku','user_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
