<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommercialVisit;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class CommercialVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Generate 3 random commercial visits
        for ($i = 0; $i < 3; $i++) {
            CommercialVisit::create([
                'user_id' => User::where('role', 'commercial')->first()->id, // Ensure to fetch a 'commercial' user
                'client_name' => $faker->company,
                'location' => $faker->address,
                'cleaning_type' => $faker->word,
                'visit_date' => Carbon::now()->addDays(rand(1, 7)),
                'contact' => $faker->phoneNumber,
                'relance_date' => Carbon::now()->addDays(rand(7, 14)),
            ]);
        }
    }
}
