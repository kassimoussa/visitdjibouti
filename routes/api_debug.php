<?php

use App\Models\Tour;
use App\Models\TourReservation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/debug/tour-reservations', function () {
    $debug = [];

    // Check if table exists
    $debug['table_exists'] = Schema::hasTable('tour_reservations');

    if ($debug['table_exists']) {
        $debug['columns'] = Schema::getColumnListing('tour_reservations');
    }

    // Check tours table
    $debug['tours_table_exists'] = Schema::hasTable('tours');
    if ($debug['tours_table_exists']) {
        $debug['tours_columns'] = Schema::getColumnListing('tours');
        $debug['tour_1_exists'] = Tour::where('id', 1)->exists();

        if ($debug['tour_1_exists']) {
            $tour = Tour::find(1);
            $debug['tour_1_data'] = [
                'id' => $tour->id,
                'max_participants' => $tour->max_participants,
                'current_participants' => $tour->current_participants ?? 'NULL',
                'status' => $tour->status,
            ];
        }
    }

    // Check app_users table
    $debug['app_users_table_exists'] = Schema::hasTable('app_users');

    return response()->json($debug);
});
