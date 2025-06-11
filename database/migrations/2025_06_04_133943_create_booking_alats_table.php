<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingAlatsTable extends Migration
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
            $table->foreignId('alat_test_id')->constrained('alat_tests');
            $table->foreignId('user_id')->constrained();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('purpose');
            $table->enum('status', ['PENDING', 'DISETUJUI', 'DIKEMBALIKAN', 'DITOLAK'])->default('PENDING');
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
        Schema::table('booking_alats', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
