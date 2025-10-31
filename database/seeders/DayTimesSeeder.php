<?php

namespace Database\Seeders;

use App\Models\DayTime;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DayTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DayTime::truncate();

        DayTime::insert([
            [
                'day' => 1, 
                'start_time' => '07:20', 
                'end_time' => '10:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 1, 
                'start_time' => '08:00', 
                'end_time' => '12:00', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 1, 
                'start_time' => '12:20', 
                'end_time' => '15:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 2, 
                'start_time' => '07:20', 
                'end_time' => '10:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 2, 
                'start_time' => '08:00', 
                'end_time' => '12:00', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 2, 
                'start_time' => '12:20', 
                'end_time' => '15:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 3, 
                'start_time' => '07:20', 
                'end_time' => '10:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 3, 
                'start_time' => '08:00', 
                'end_time' => '12:00', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 3, 
                'start_time' => '12:20', 
                'end_time' => '15:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 4, 
                'start_time' => '07:20', 
                'end_time' => '10:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 4, 
                'start_time' => '08:00', 
                'end_time' => '12:00', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 4, 
                'start_time' => '12:20', 
                'end_time' => '15:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 5, 
                'start_time' => '07:20', 
                'end_time' => '10:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 5, 
                'start_time' => '08:00', 
                'end_time' => '12:00', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 5, 
                'start_time' => '13:10', 
                'end_time' => '16:00', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 6, 
                'start_time' => '07:20', 
                'end_time' => '10:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], [
                'day' => 6, 
                'start_time' => '12:20', 
                'end_time' => '15:10', 
                'created_at' => Carbon::now(), 
                'updated_at' => Carbon::now(), 
            ], 
        ]);
    }
}
