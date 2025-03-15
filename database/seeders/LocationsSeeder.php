<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Locations\Country;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(storage_path('app/countries/filtered_country.json'));
        // تحويل النص إلى مصفوفة
        $countries = json_decode($json, true); // استخدم true لتحويل إلى مصفوفة

        // استرجاع البيانات المطلوبة وإدخالها في قاعدة البيانات
        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'id' => $country['id'],
                'name' => $country['name'],
                'code' => $country['country_code'],
                'phone_code' => $country['phone_code'],
                'capital' => $country['capital'],
                'currency' => $country['currency'],
                'currency_name' => $country['currency_name'],
                'currency_symbol' => $country['currency_symbol'],
            ]);
        }


        $json = File::get(storage_path('app/countries/filtered_states.json'));

        $states = json_decode($json, true);

        foreach ($states as $state) {
            DB::table('states')->insert([
                'name' => $state['name'],
                'country_id' => $state['country_id'],
                'country_code' => $state['country_code'],
                'state_code' => $state['state_code'],
                'state_id' => $state['state_id'],
            ]);
        }

        $json = File::get(storage_path('app/countries/filtered_cities.json'));

        $cities = json_decode($json, true);

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'name' => $city['name'],
                'state_id' => $city['state_id'],
                'state_code' => $city['state_code'],
                'country_code' => $city['country_code'],
            ]);
        }
    }
}
