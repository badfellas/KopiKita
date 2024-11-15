<?php

namespace Database\Factories;

use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PemesananFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pemesanan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_pesanan' => strtoupper($this->faker->regexify('[A-Za-z]{3}-[A-Za-z]{3}-[A-Za-z]{3}')),
            'user_id' => \App\Models\User::factory()->create()->id,
            'tanggal' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['pending', 'selesai']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
