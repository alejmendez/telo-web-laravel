<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Crm\Models\City;
use Modules\Crm\Models\Commune;
use Modules\Crm\Models\Country;

class CountriesCitiesCommunesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Chile' => [
                'iso2' => 'CL',
                'Santiago' => ['Providencia', 'Las Condes'],
            ],
            'Argentina' => [
                'iso2' => 'AR',
                'CABA' => ['Palermo', 'Recoleta'],
            ],
        ];

        foreach ($data as $countryName => $info) {
            $country = Country::query()->updateOrCreate(
                ['name' => $countryName],
                ['iso2' => $info['iso2']]
            );

            foreach ($info as $cityName => $communes) {
                if ($cityName === 'iso2') {
                    continue;
                }
                $city = City::query()->updateOrCreate(
                    ['country_id' => $country->id, 'name' => $cityName],
                    []
                );
                foreach ($communes as $communeName) {
                    Commune::query()->updateOrCreate(
                        ['city_id' => $city->id, 'name' => $communeName],
                        []
                    );
                }
            }
        }
    }
}
