<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlotColumnsToRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'plot_image')) {
                $table->string('plot_image')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('rooms', 'plot_description')) {
                $table->text('plot_description')->nullable()->after('plot_image');
            }
            if (!Schema::hasColumn('rooms', 'plot_valid_from')) {
                $table->date('plot_valid_from')->nullable()->after('plot_description');
            }
            if (!Schema::hasColumn('rooms', 'plot_valid_until')) {
                $table->date('plot_valid_until')->nullable()->after('plot_valid_from');
            }
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['plot_image', 'plot_description', 'plot_valid_from', 'plot_valid_until']);
        });
    }
}
