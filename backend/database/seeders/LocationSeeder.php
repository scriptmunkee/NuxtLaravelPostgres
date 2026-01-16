<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            // London
            ['country' => 'UK', 'county' => 'Greater London', 'city' => 'London', 'postcode' => null],

            // England - South East
            ['country' => 'UK', 'county' => 'Kent', 'city' => 'Canterbury', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Kent', 'city' => 'Maidstone', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Surrey', 'city' => 'Guildford', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Sussex', 'city' => 'Brighton', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Hampshire', 'city' => 'Southampton', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Hampshire', 'city' => 'Portsmouth', 'postcode' => null],

            // England - South West
            ['country' => 'UK', 'county' => 'Bristol', 'city' => 'Bristol', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Devon', 'city' => 'Exeter', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Cornwall', 'city' => 'Truro', 'postcode' => null],

            // England - Midlands
            ['country' => 'UK', 'county' => 'West Midlands', 'city' => 'Birmingham', 'postcode' => null],
            ['country' => 'UK', 'county' => 'West Midlands', 'city' => 'Coventry', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Nottinghamshire', 'city' => 'Nottingham', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Leicestershire', 'city' => 'Leicester', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Derbyshire', 'city' => 'Derby', 'postcode' => null],

            // England - North West
            ['country' => 'UK', 'county' => 'Greater Manchester', 'city' => 'Manchester', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Merseyside', 'city' => 'Liverpool', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Lancashire', 'city' => 'Preston', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Cheshire', 'city' => 'Chester', 'postcode' => null],

            // England - North East
            ['country' => 'UK', 'county' => 'Tyne and Wear', 'city' => 'Newcastle upon Tyne', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Durham', 'city' => 'Durham', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Northumberland', 'city' => 'Alnwick', 'postcode' => null],

            // England - Yorkshire
            ['country' => 'UK', 'county' => 'West Yorkshire', 'city' => 'Leeds', 'postcode' => null],
            ['country' => 'UK', 'county' => 'South Yorkshire', 'city' => 'Sheffield', 'postcode' => null],
            ['country' => 'UK', 'county' => 'North Yorkshire', 'city' => 'York', 'postcode' => null],

            // Scotland
            ['country' => 'UK', 'county' => 'Edinburgh', 'city' => 'Edinburgh', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Glasgow', 'city' => 'Glasgow', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Aberdeen', 'city' => 'Aberdeen', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Dundee', 'city' => 'Dundee', 'postcode' => null],

            // Wales
            ['country' => 'UK', 'county' => 'Cardiff', 'city' => 'Cardiff', 'postcode' => null],
            ['country' => 'UK', 'county' => 'Swansea', 'city' => 'Swansea', 'postcode' => null],

            // Northern Ireland
            ['country' => 'UK', 'county' => 'Belfast', 'city' => 'Belfast', 'postcode' => null],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
