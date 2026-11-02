<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectionReasonToBookingListsTable extends Migration
{
    public function up()
    {
        Schema::table('booking_lists', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('booking_lists', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
}
