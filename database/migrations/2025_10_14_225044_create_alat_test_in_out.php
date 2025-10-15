<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlatTestInOut extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alat_test_in_outs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description');
            $table->enum('type', array('Masuk', 'Keluar'))->default('Masuk');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('alat_test_in_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_test_in_out_id');
            $table->foreignId('alat_test_item_id');
            $table->double('quantity')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alat_test_in_out_items');
        Schema::dropIfExists('alat_test_in_outs');
    }
}
