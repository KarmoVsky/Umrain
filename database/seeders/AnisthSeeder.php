<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AnisthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'name' => 'car_layout_item_search',
                'val' => 'grid',
                'group' => "car"
            ],
            [
                'name' => 'flight_layout_item_search',
                'val' => 'grid',
                'group' => "flight"
            ],
            [
                'name' => 'tour_layout_item_search',
                'val' => 'grid',
                'group' => "tour"
            ],
            [
                'name' => 'hotel_allow_customer_can_change_their_booking_status',
                'val' => '1',
                'group' => 'hotel'
            ],
            [
                'name' => 'vendor_commission_calculate_way',
                'val' => 'Dedict',
                'group' => 'hotel'
            ],
            [
                'name' => 'vendor_commission_calculate_time',
                'val' => 'One_Time',
                'group' => 'hotel'
            ],
            [
                'name' => 'per_person',
                'val' => '0',
                'group' => 'hotel'
            ],
            [
                'name' => 'user_enable_permanently_delete',
                'val' => '1',
                'group' => ' '
            ],
            [
                'name' => 'vendor_commission_availability_type',
                'val' => ' ',
                'group' => ' '
            ]

        ];
        Artisan::call('optimize:clear');
        foreach($settings as $item) {
            DB::table('core_settings')->updateOrInsert($item);
        }

    }
}
