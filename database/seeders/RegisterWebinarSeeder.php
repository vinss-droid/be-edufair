<?php

namespace Database\Seeders;

use App\Models\RegisterWebinar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RegisterWebinarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();

        for ($i = 1; $i <= 300; $i++) {
            RegisterWebinar::create([
                'user_id' => $faker->uuid(),
                'year' => '2023',
                'name' => $faker->name(),
                'email' => $faker->email(),
                'no_whatsapp' => $faker->phoneNumber(),
                'agency_name' => $faker->company(),
                'province' => $faker->city(),
                'regency' => $faker->city(),
                'proof_himsika' => $faker->imageUrl(),
                'proof_edufair' => $faker->imageUrl()
            ]);
        }

    }
}
