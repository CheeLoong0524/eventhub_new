<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Venue;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venues = [
            [
                'name' => 'Kuala Lumpur Convention Centre',
                'location' => 'Kuala Lumpur City Centre, KL',
                'capacity' => 5000,
            ],
            [
                'name' => 'Putra World Trade Centre',
                'location' => 'Jalan Tun Ismail, KL',
                'capacity' => 3000,
            ],
            [
                'name' => 'Malaysia International Trade Centre',
                'location' => 'Jalan Sultan Haji Ahmad Shah, KL',
                'capacity' => 2000,
            ],
            [
                'name' => 'Sunway Pyramid Convention Centre',
                'location' => 'Bandar Sunway, Selangor',
                'capacity' => 1500,
            ],
            [
                'name' => 'KLCC Suria Mall',
                'location' => 'Kuala Lumpur City Centre, KL',
                'capacity' => 800,
            ],
            [
                'name' => 'Pavilion Kuala Lumpur',
                'location' => 'Bukit Bintang, KL',
                'capacity' => 600,
            ],
            [
                'name' => 'One Utama Shopping Centre',
                'location' => 'Bandar Utama, Selangor',
                'capacity' => 500,
            ],
            [
                'name' => 'Mid Valley Megamall',
                'location' => 'Mid Valley City, KL',
                'capacity' => 400,
            ],
            [
                'name' => 'IOI City Mall',
                'location' => 'Putrajaya, Selangor',
                'capacity' => 350,
            ],
            [
                'name' => 'The Gardens Mall',
                'location' => 'Mid Valley City, KL',
                'capacity' => 300,
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }
    }
}