<?php

namespace App\Http\Controllers;

use App\Models\Locations\Country;
use App\Models\Locations\State;
use App\Models\Locations\City;

class LocationController extends Controller
{
    public function getCountries()
    {
        return Country::all();
    }

    public function getStates($country_id)
    {

        $states = State::where('country_id', $country_id)
            ->orWhere('country_code', $country_id)
            ->get();

        if ($states->isEmpty()) {
            return response()->json(['message' => 'There are no available states for this country.'], 404);
        }

        return response()->json($states);
    }

    public function getCities($country_code, $state_code)
    {
        $cities = City::where('country_code', $country_code)
            ->where('state_code', $state_code)->get();

        if ($cities->isEmpty()) {
            return response()->json(['message' => 'No cities available for this state.'], 404);
        }

        return response()->json($cities);
    }
}
