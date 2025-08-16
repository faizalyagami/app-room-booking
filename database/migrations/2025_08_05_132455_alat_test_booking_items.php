<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlatTestBookingItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_alats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('purpose');
            $table->enum('status', ['PENDING', 'DISETUJUI', 'DIKEMBALIKAN', 'DITOLAK'])->default('PENDING');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('booking_alat_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_alat_id');
            $table->foreignId('alat_test_item_id');
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
        Schema::table('booking_alat_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('booking_alats', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
