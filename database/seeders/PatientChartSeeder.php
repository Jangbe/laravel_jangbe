<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientChartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = Hospital::select('id')->get()->pluck('id')->toArray();

        $patients = Patient::factory(1000)->make();
        $patients = $patients->map(function ($patient) use ($hospitals) {
            $date = fake()->dateTimeBetween(
                now()->startOfYear(),
                now()
            );
            return [
                ...$patient->toArray(),
                'hospital_id' => fake()->randomElement($hospitals),
                'created_at' => $date,
            ];
        });

        Patient::insert($patients->toArray());
    }
}
