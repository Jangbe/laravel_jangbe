<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = Hospital::select('id')->get()->pluck('id')->toArray();

        $patients = Patient::factory(10)->make();
        $patients = $patients->map(fn($patient) => [
            ...$patient->toArray(),
            'hospital_id' => fake()->randomElement($hospitals),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Patient::insert($patients->toArray());
    }
}
