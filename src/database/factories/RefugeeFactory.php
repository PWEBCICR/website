<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Refugee;
use App\Models\Route;
use App\Models\Country;
use App\Models\Role;
use App\Models\Gender;

class RefugeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Refugee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'date' => $this->faker->date("Y-m-d", $max = 'now', $min='- 2 months'),
            'unique_id' => $this->faker->regexify('[A-Z]{3}-[0-9]{6}'),
            'nationality' => Country::inRandomOrder()->first()->id,
            'full_name' => $this->faker->name,
            'alias' => $this->faker->name,
            'other_names' => $this->faker->name,
            'mothers_names' => $this->faker->name,
            'fathers_names' => $this->faker->name,
            'fathers_names' => $this->faker->name,
            'role' => Role::inRandomOrder()->first()->id,
            'age' => $this->faker->numberBetween(1,120),
            'birth_date' => $this->faker->date("Y-m-d", $max = 'now'),
            'date_last_seen' => $this->faker->date("Y-m-d", $min = '-2 years', $max="now"),
            'birth_place' => $this->faker->city,
            'gender' => Gender::inRandomOrder()->first()->id,
            'passport_number' => $this->faker->regexify('[A-Z]{3}-[0-9]{6}-[A-Z]{8}'),
            'embarkation_date' => $this->faker->date("Y-m-d", $max = 'now'),
            'detention_place' => $this->faker->randomElement(['Libya', '', 'Aleppo', 'Sahara']),
            'embarkation_place' => $this->faker->randomElement(['Libya', 'Roubaix', 'Tunisia', 'Algeria']),
            'destination' => $this->faker->randomElement(['France', 'Spain', 'Italy', 'Greece']),
            'route' => Route::inRandomOrder()->first()->id,
            'residence' => Country::inRandomOrder()->first()->id,

        ];
    }
}
