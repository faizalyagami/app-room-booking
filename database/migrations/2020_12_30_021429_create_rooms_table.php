<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 100)->nullable();
            $table->integer('capacity')->nullable();
            $table->string('photo')->nullable();
            $table->string('plot_image')->nullable();
            $table->text('plot_description')->nullable();
            $table->date('plot_valid_from')->nullable();
            $table->date('plot_valid_until')->nullable();
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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['plot_image', 'plot_description', 'plot_valid_from', 'plot_valid_until']);
        });
    }
}
